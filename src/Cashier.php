<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\{Adjusters, Adjustment, Adjustments};
use Vaened\PriceEngine\Adjusters\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Money\{AdjustmentManager, Amount, Charge, Discount, Prices\Price};

abstract class Cashier implements TotalSummary
{
    private readonly Money             $unitPrice;

    private readonly TaxCodes          $applicableCodes;

    private Price                      $price;

    private readonly AdjustmentManager $discounts;

    private readonly AdjustmentManager $charges;

    private readonly AdjustmentManager $taxes;

    public function __construct(
        Amount      $amount,
        private int $quantity,
        Taxes       $taxes = new Taxes([]),
        Adjusters   $charges = new Adjusters([]),
        Adjusters   $discounts = new Adjusters([]),
    )
    {
        $this->applicableCodes = $amount->applicableCodes();
        $allTaxes              = $taxes->additionally($amount->taxes())->onlyAdjustablesOf($this->applicableCodes);
        $applicableTaxes       = $allTaxes->toAdjusters();

        $this->unitPrice = $allTaxes->clean($amount->value());
        $this->price     = $this->createUnitPrice($this->unitPrice, $applicableTaxes, $discounts, $charges);
        $this->createAdjustmentManagers($discounts, $charges, $applicableTaxes, $this->price, $this->quantity);
    }

    abstract protected function createUnitPrice(
        Money     $unitPrice,
        Adjusters $taxes,
        Adjusters $discounts,
        Adjusters $charges
    ): Price;

    public function update(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->discounts->update($quantity);
        $this->charges->update($quantity);
        $this->taxes->update($quantity);
    }

    public function apply(Discount ...$discounts): void
    {
        $this->discounts->add($discounts);
        $this->syncPrices();
    }

    public function cancelDiscount(BackedEnum|UnitEnum|string $discountCode): void
    {
        $this->discounts->remove($discountCode);
        $this->syncPrices();
    }

    public function discount(BackedEnum|UnitEnum|string $discountCode): ?Adjustment
    {
        return $this->discounts->locate($discountCode);
    }

    public function discounts(): Adjustments
    {
        return $this->discounts->adjustments();
    }

    public function add(Charge ...$charges): void
    {
        $this->charges->add($charges);
        $this->syncPrices();
    }

    public function revertCharge(BackedEnum|UnitEnum|string $chargeCode): void
    {
        $this->charges->remove($chargeCode);
        $this->syncPrices();
    }

    public function charge(BackedEnum|UnitEnum|string $chargeCode): ?Adjustment
    {
        return $this->charges->locate($chargeCode);
    }

    public function charges(): Adjustments
    {
        return $this->charges->adjustments();
    }

    public function tax(BackedEnum|UnitEnum|string $taxCode): ?Adjustment
    {
        return $this->taxes->locate($taxCode);
    }

    public function taxes(): Adjustments
    {
        return $this->taxes->adjustments();
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function subtotal(): Money
    {
        return $this->unitPrice()->multipliedBy($this->quantity());
    }

    public function totalTaxes(): Money
    {
        if ($this->applicableCodes->isNothingAllowed()) {
            return Money::zero($this->price->currency(), $this->price->context());
        }

        return $this->taxes->total();
    }

    public function totalCharges(): Money
    {
        return $this->charges->total();
    }

    public function totalDiscounts(): Money
    {
        return $this->discounts->total();
    }

    public function total(): Money
    {
        return $this->subtotal()
                    ->plus($this->totalTaxes())
                    ->plus($this->totalCharges())
                    ->minus($this->totalDiscounts());
    }

    protected function syncPrices(): void
    {
        $this->price = $this->createUnitPrice(
            $this->unitPrice,
            $this->taxes->adjusters(),
            $this->discounts->adjusters(),
            $this->charges->adjusters(),
        );
        $this->discounts->revalue($this->price->discountable());
        $this->charges->revalue($this->price->chargeable());
        $this->taxes->revalue($this->price->taxable());
    }

    private function createAdjustmentManagers(
        Adjusters $discounts,
        Adjusters $charges,
        Adjusters $taxes,
        Price     $price,
        int       $quantity
    ): void
    {
        $this->discounts = new AdjustmentManager($discounts, $price->discountable(), $quantity);
        $this->charges   = new AdjustmentManager($charges, $price->chargeable(), $quantity);
        $this->taxes     = new AdjustmentManager($taxes, $price->taxable(), $quantity);
    }
}
