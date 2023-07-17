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
use Vaened\PriceEngine\Money\{Amount, Charge, Discount, Prices\Price};
use function Lambdish\Phunctional\each;

abstract class Cashier implements TotalSummary
{
    private readonly Money     $unitPrice;
    private readonly TaxCodes  $applicableCodes;
    private readonly Adjusters $taxesAdjusters;
    private Price              $price;

    public function __construct(
        Amount                     $amount,
        private int                $quantity,
        Taxes                      $taxes = new Taxes([]),
        private readonly Adjusters $charges = new Adjusters([]),
        private readonly Adjusters $subtractors = new Adjusters([]),
    )
    {
        $this->applicableCodes = $amount->applicableCodes();
        $allTaxes              = $taxes->additionally($amount->taxes())->onlyAdjustablesOf($this->applicableCodes);
        $this->taxesAdjusters  = $allTaxes->toAdjusters();
        $this->unitPrice       = $allTaxes->clean($amount->value());
        $this->syncUniPrice();
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
    }

    public function apply(Discount ...$discounts): void
    {
        each(static fn(Discount $discount) => $this->subtractors->create($discount), $discounts);
        $this->syncUniPrice();
    }

    public function cancelDiscount(BackedEnum|UnitEnum|string $discountCode): void
    {
        $this->subtractors->remove($discountCode);
        $this->syncUniPrice();
    }

    public function discount(BackedEnum|UnitEnum|string $discountCode): ?Adjustment
    {
        return $this->subtractors->locate($discountCode);
    }

    public function discounts(): Adjustments
    {
        return $this->subtractors->adjustments(
            $this->price->discountable()->multipliedBy($this->quantity)
        );
    }

    public function add(Charge ...$charges): void
    {
        each(static fn(Charge $charge) => $this->charges->create($charge), $charges);
        $this->syncUniPrice();
    }

    public function revertCharge(BackedEnum|UnitEnum|string $chargeCode): void
    {
        $this->charges->remove($chargeCode);
        $this->syncUniPrice();
    }

    public function charge(BackedEnum|UnitEnum|string $chargeCode): ?Adjustment
    {
        return $this->charges->locate($chargeCode);
    }

    public function charges(): Adjustments
    {
        return $this->charges->adjustments(
            $this->price->chargeable()->multipliedBy($this->quantity)
        );
    }

    public function tax(BackedEnum|UnitEnum|string $taxCode): ?Adjustment
    {
        return $this->taxesAdjusters->locate($taxCode);
    }

    public function taxes(): Adjustments
    {
        return $this->taxesAdjusters->adjustments(
            $this->price->taxable()->multipliedBy($this->quantity)
        );
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

        return $this->taxesAdjusters->apply(
            $this->price->taxable()->multipliedBy($this->quantity)
        );
    }

    public function totalCharges(): Money
    {
        return $this->charges->apply(
            $this->price->chargeable()->multipliedBy($this->quantity)
        );
    }

    public function totaDiscounts(): Money
    {
        return $this->subtractors->apply(
            $this->price->discountable()->multipliedBy($this->quantity)
        );
    }

    public function total(): Money
    {
        return $this->subtotal()
            ->plus($this->totalTaxes())
            ->plus($this->totalCharges())
            ->minus($this->totaDiscounts());
    }

    protected function syncUniPrice(): void
    {
        $this->price = $this->createUnitPrice(
            $this->unitPrice,
            $this->taxesAdjusters,
            $this->subtractors,
            $this->charges,
        );
    }
}
