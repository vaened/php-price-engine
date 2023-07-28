<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\Adjusters;
use Vaened\PriceEngine\Adjustments\AdjusterScheme;
use Vaened\PriceEngine\Handlers\ExclusiveAdjustmentHandler;
use Vaened\PriceEngine\Money\Concerns\Cacheable;

use function Lambdish\Phunctional\each;

final class AdjustmentManager
{
    use Cacheable;

    protected Adjustments $adjustments;

    public function __construct(
        private readonly Adjusters $adjusters,
        private readonly Money     $unitPrice,
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
        each(fn(AdjusterScheme $adjuster) => $this->adjusters->push($adjuster), $adjusters);
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
            $this->adjusters->map($this->createAdjustment()),
            $this->unitPrice->getCurrency(),
            $this->unitPrice->getContext(),
        );
    }

    protected function createAdjustment(): callable
    {
        return fn(AdjusterScheme $adjuster) => new Adjustment(
            ExclusiveAdjustmentHandler::apply($this->unitPrice, $this->quantity, $adjuster),
            $adjuster->type(),
            $adjuster->mode(),
            $adjuster->value(),
            $adjuster->code()
        );
    }
}
