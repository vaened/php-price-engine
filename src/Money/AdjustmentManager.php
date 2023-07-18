<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use BackedEnum;
use Brick\Math\BigDecimal;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\Adjusters;
use Vaened\PriceEngine\Adjusters\Adjustment;
use Vaened\PriceEngine\Adjusters\Adjustments;
use function Lambdish\Phunctional\each;

final class AdjustmentManager
{
    protected Adjustments $adjustments;
    private BigDecimal    $lasAmount;
    private Money         $subtotal;

    public function __construct(
        private readonly Adjusters $adjusters,
        private Money              $unitPrice,
        private int                $quantity,
    )
    {
        $this->forceRecalculation();
        $this->calculateSubtotal();
        $this->breakdownAdjustment();
    }

    public function total(): Money
    {
        return $this->adjustments()->total();
    }

    public function add(array $adjusters): void
    {
        each(fn(Adjuster $adjuster) => $this->adjusters->create($adjuster), $adjusters);
        $this->forceRecalculation();
    }

    public function remove(BackedEnum|UnitEnum|string $adjusterCode): void
    {
        $this->adjusters->remove($adjusterCode);
        $this->forceRecalculation();
    }

    public function locate(BackedEnum|UnitEnum|string $adjusterCode): Adjustment
    {
        return $this->adjustments()->locate($adjusterCode);
    }

    public function update(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->calculateSubtotal();
    }

    public function revalue(Money $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
        $this->calculateSubtotal();
    }

    public function adjustments(): Adjustments
    {
        if ($this->needsRecalculation()) {
            $this->breakdownAdjustment();
        }

        return $this->adjustments;
    }

    public function adjusters(): Adjusters
    {
        return $this->adjusters;
    }

    public function subtotal(): Money
    {
        return $this->subtotal;
    }

    protected function breakdownAdjustment(): void
    {
        $subtotal          = $this->subtotal();
        $this->adjustments = Adjustments::from(
            $subtotal->getCurrency(),
            $subtotal->getContext(),
            $this->adjusters->map($this->createAdjustment($subtotal))
        );
    }

    protected function createAdjustment(Money $money): callable
    {
        return static fn(Adjuster $adjuster) => new Adjustment(
            $adjuster->adjust($money), $adjuster->type(), $adjuster->value(), $adjuster->code()
        );
    }

    private function calculateSubtotal(): void
    {
        $this->subtotal = $this->unitPrice->multipliedBy($this->quantity);
    }

    private function needsRecalculation(): bool
    {
        $current = $this->subtotal()->getAmount()->toBigDecimal();

        if ($this->lasAmount->compareTo($current) === 0) {
            return false;
        }

        $this->lasAmount = $current;
        return true;
    }

    private function forceRecalculation(): void
    {
        $this->lasAmount = BigDecimal::of(-1);
    }
}
