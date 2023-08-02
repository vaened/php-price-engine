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
use Vaened\PriceEngine\Concerns\Cacheable;
use Vaened\PriceEngine\Handlers\ExclusiveAdjustmentHandler;

use function Lambdish\Phunctional\each;
use function sprintf;

final class AdjustmentManager
{
    use Cacheable {
        requiresUpdate as requiresRecalculation;
        cleanCache as forceRecalculation;
    }

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

    public static function totalize(Money $price, Adjusters $adjustersToApply): Money
    {
        return (new self($adjustersToApply, $price, 1))->total();
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
        return $this->requiresRecalculation()
            ? $this->adjustments = $this->breakdownAdjustment()
            : $this->adjustments;
    }

    public function adjusters(): Adjusters
    {
        return $this->adjusters;
    }

    protected function cacheIdentifier(): string
    {
        return sprintf('[%s]X[%d]', $this->unitPrice->getAmount()->__toString(), $this->quantity);
    }

    private function breakdownAdjustment(): Adjustments
    {
        return new Adjustments(
            $this->adjusters->map($this->createAdjustment()),
            $this->unitPrice->getCurrency(),
            $this->unitPrice->getContext(),
        );
    }

    private function createAdjustment(): callable
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
