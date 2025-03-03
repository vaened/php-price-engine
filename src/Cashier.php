<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\{Adjusters, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Tax\{PriceGrosser, Taxes};
use Vaened\PriceEngine\Money\{Amount};

use function dd;

abstract class Cashier implements Summary
{
    protected AdjustmentManager $discounts;

    protected AdjustmentManager $charges;

    private AdjustmentManager   $taxes;

    private Price               $price;

    private UnitRate            $unitRate;

    public function __construct(
        Amount      $amount,
        private int $quantity,
        Taxes       $taxes = new Taxes([]),
        Adjusters   $charges = new Adjusters([]),
        Adjusters   $discounts = new Adjusters([]),
    )
    {
        $allTaxes        = $taxes->merge($amount->taxes())
                                 ->only($amount->applicableCodes());
        $applicableTaxes = $allTaxes->toAdjusters();

        $this->initializePrice(
            netUnitPrice: PriceGrosser::for($allTaxes)->clean($amount->value()),
            taxes       : $applicableTaxes
        );
        $this->initializeMoneyAdjusters($discounts, $charges, $applicableTaxes);
    }

    abstract protected function createUnitRate(Price $price): UnitRate;

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
    }

    public function cancelDiscount(BackedEnum|UnitEnum|string $discountCode): void
    {
        $this->discounts->remove($discountCode);
    }

    public function discounts(): Adjustments
    {
        return $this->discounts->adjustments();
    }

    public function add(Charge ...$charges): void
    {
        $this->charges->add($charges);
    }

    public function revertCharge(BackedEnum|UnitEnum|string $chargeCode): void
    {
        $this->charges->remove($chargeCode);
    }

    public function charges(): Adjustments
    {
        return $this->charges->adjustments();
    }

    public function taxes(): Adjustments
    {
        return $this->taxes->adjustments();
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function priceBreakdown(): UnitRate
    {
        return $this->unitRate;
    }

    public function unitPrice(): Price
    {
        return $this->price;
    }

    public function subtotal(): Price
    {
        return $this->unitPrice()->multipliedBy($this->quantity());
    }

    public function total(): Money
    {
        return $this->subtotal()
                    ->net()
                    ->plus($this->taxes()->total())
                    ->plus($this->charges()->total())
                    ->minus($this->discounts()->total());
    }

    protected function initializePrice(Money $netUnitPrice, Adjusters $taxes): void
    {
        $this->price = new Price(
            $netUnitPrice,
            grossUnitPrice: $netUnitPrice->plus(
                AdjustmentManager::totalize($netUnitPrice, $taxes)
            ),
        );
    }

    protected function initializeMoneyAdjusters(Adjusters $discounts, Adjusters $charges, Adjusters $taxes): void
    {
        $this->unitRate  = $this->createUnitRate($this->price);
        $this->discounts = new AdjustmentManager($discounts, $this->unitRate->discountable(), $this->quantity);
        $this->charges   = new AdjustmentManager($charges, $this->unitRate->chargeable(), $this->quantity);
        $this->taxes     = new AdjustmentManager($taxes, $this->unitRate->taxable(), $this->quantity);
    }
}
