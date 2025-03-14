<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\{Adjustments, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Taxation\{TaxStripper, Taxes};
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
        Adjustments $charges = new Adjustments([]),
        Adjustments $discounts = new Adjustments([]),
    )
    {
        $allTaxes        = $taxes->merge($amount->taxes())
                                 ->only($amount->applicableCodes());
        $applicableTaxes = $allTaxes->toAdjustments();

        $this->initializePrice(
            netUnitPrice: TaxStripper::for($allTaxes)->clean($amount->value()),
            taxes       : $applicableTaxes
        );
        $this->initializeMoneyAdjustments($discounts, $charges, $applicableTaxes);
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

    public function discounts(): Modifiers
    {
        return $this->discounts->modifiers();
    }

    public function add(Charge ...$charges): void
    {
        $this->charges->add($charges);
    }

    public function revertCharge(BackedEnum|UnitEnum|string $chargeCode): void
    {
        $this->charges->remove($chargeCode);
    }

    public function charges(): Modifiers
    {
        return $this->charges->modifiers();
    }

    public function taxes(): Modifiers
    {
        return $this->taxes->modifiers();
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

    protected function initializePrice(Money $netUnitPrice, Adjustments $taxes): void
    {
        $this->price = new Price(
            $netUnitPrice,
            grossUnitPrice: $netUnitPrice->plus(
                AdjustmentManager::totalize($netUnitPrice, $taxes)
            ),
        );
    }

    protected function initializeMoneyAdjustments(Adjustments $discounts, Adjustments $charges, Adjustments $taxes): void
    {
        $this->unitRate  = $this->createUnitRate($this->price);
        $this->discounts = new AdjustmentManager($discounts, $this->unitRate->discountable(), $this->quantity);
        $this->charges   = new AdjustmentManager($charges, $this->unitRate->chargeable(), $this->quantity);
        $this->taxes     = new AdjustmentManager($taxes, $this->unitRate->taxable(), $this->quantity);
    }
}
