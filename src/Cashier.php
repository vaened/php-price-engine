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

abstract class Cashier implements TotalSummary
{
    protected AdjustmentManager $discounts;

    protected AdjustmentManager $charges;

    private AdjustmentManager   $taxes;

    private readonly Money      $grossUnitPrice;

    private UnitRate            $unitRate;

    public function __construct(
        Amount      $amount,
        private int $quantity,
        Taxes       $taxes = new Taxes([]),
        Adjusters   $charges = new Adjusters([]),
        Adjusters   $discounts = new Adjusters([]),
    )
    {
        $allTaxes             = $taxes->additionally($amount->taxes())
                                      ->onlyAdjustablesOf($amount->applicableCodes());
        $applicableTaxes      = $allTaxes->toAdjusters();
        $this->grossUnitPrice = PriceGrosser::for($allTaxes)->clean($amount->value());
        $this->unitRate       = $this->createUnitRate($this->grossUnitPrice, $applicableTaxes);

        $this->initializeMoneyAdjusters($discounts, $charges, $applicableTaxes);
    }

    abstract protected function createUnitRate(Money $grossUnitPrice, Adjusters $taxes): UnitRate;

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

    public function unitPrice(): Money
    {
        return $this->grossUnitPrice;
    }

    public function subtotal(): Money
    {
        return $this->unitPrice()->multipliedBy($this->quantity());
    }

    public function total(): Money
    {
        return $this->subtotal()
                    ->plus($this->taxes()->total())
                    ->plus($this->charges()->total())
                    ->minus($this->discounts()->total());
    }

    protected function initializeMoneyAdjusters(Adjusters $discounts, Adjusters $charges, Adjusters $taxes): void
    {
        $this->discounts = new AdjustmentManager($discounts, $this->unitRate->discountable(), $this->quantity);
        $this->charges   = new AdjustmentManager($charges, $this->unitRate->chargeable(), $this->quantity);
        $this->taxes     = new AdjustmentManager($taxes, $this->unitRate->taxable(), $this->quantity);
    }
}
