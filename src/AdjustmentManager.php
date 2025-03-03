<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\Adjustments;
use Vaened\PriceEngine\Adjustments\AdjustmentScheme;
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

    protected Modifiers $modifiers;

    public function __construct(
        private readonly Adjustments $adjustments,
        private Money                $unitPrice,
        private int                  $quantity,
    )
    {
        $this->forceRecalculation();
        $this->breakdownAdjustment();
    }

    public static function totalize(Money $price, Adjustments $adjustmentsToApply): Money
    {
        return (new self($adjustmentsToApply, $price, 1))->total();
    }

    public function total(): Money
    {
        return $this->modifiers()->total();
    }

    public function add(array $adjustments): void
    {
        each(fn(AdjustmentScheme $adjustment) => $this->adjustments->push($adjustment), $adjustments);
        $this->forceRecalculation();
    }

    public function remove(BackedEnum|UnitEnum|string $adjustmentCode): void
    {
        $this->adjustments->remove($adjustmentCode);
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

    public function modifiers(): Modifiers
    {
        return $this->requiresRecalculation()
            ? $this->modifiers = $this->breakdownAdjustment()
            : $this->modifiers;
    }

    public function adjustments(): Adjustments
    {
        return $this->adjustments;
    }

    protected function cacheIdentifier(): string
    {
        return sprintf('[%s]X[%d]', $this->unitPrice->getAmount()->__toString(), $this->quantity);
    }

    private function breakdownAdjustment(): Modifiers
    {
        return new Modifiers(
            $this->adjustments->map($this->createAdjustment()),
            $this->unitPrice->getCurrency(),
            $this->unitPrice->getContext(),
        );
    }

    private function createAdjustment(): callable
    {
        return fn(AdjustmentScheme $adjustment) => new Modifier(
            ExclusiveAdjustmentHandler::apply($this->unitPrice, $this->quantity, $adjustment),
            $adjustment->type(),
            $adjustment->mode(),
            $adjustment->value(),
            $adjustment->code()
        );
    }
}
