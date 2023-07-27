<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\Adjusters;
use Vaened\PriceEngine\Adjusters\Adjustment;
use Vaened\PriceEngine\Adjusters\Adjustments;
use Vaened\PriceEngine\Adjusters\MoneyAdjuster;
use Vaened\PriceEngine\Money\Concerns\Cacheable;

use function Lambdish\Phunctional\each;

final class AdjustmentManager
{
    use Cacheable;

    protected Adjustments $adjustments;

    public function __construct(
        private readonly Adjusters $adjusters,
        private Money              $unitPrice,
        private int                $quantity,
    )
    {
        $this->forceRecalculation();
        $this->breakdownAdjustment();
    }

    public function total(): Money
    {
        return $this->adjustments()->total();
    }

    public function add(array $adjusters): void
    {
        each(fn(MoneyAdjuster $adjuster) => $this->adjusters->push($adjuster), $adjusters);
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
        $this->forceRecalculation();
    }

    public function revalue(Money $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
        $this->forceRecalculation();
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

    protected function breakdownAdjustment(): void
    {
        $this->adjustments = new Adjustments(
            $this->adjusters->map($this->createAdjustment($this->unitPrice)),
            $this->unitPrice->getCurrency(),
            $this->unitPrice->getContext(),
        );
    }

    protected function createAdjustment(Money $money): callable
    {
        return fn(MoneyAdjuster $adjuster) => new Adjustment(
            $adjuster->adjust($money)->multipliedBy($this->quantity),
            $adjuster->type(),
            $adjuster->value(),
            $adjuster->code()
        );
    }
}
