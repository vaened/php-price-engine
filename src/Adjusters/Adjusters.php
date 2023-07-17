<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

use BackedEnum;
use Brick\Math\BigDecimal;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Money\Adjuster;
use Vaened\Support\Types\ArrayObject;
use function dd;

class Adjusters extends ArrayObject
{
    protected ?Adjustments $adjustments;
    private BigDecimal $lasAmount;

    public function __construct(iterable $items)
    {
        parent::__construct($items);
        $this->forceRecalculation();
    }

    public function apply(Money $money): Money
    {
        return $this->adjustments($money)->total();
    }

    public function adjustments(Money $money): Adjustments
    {
        if (!$this->needsRecalculation($money)) {
            return $this->adjustments ??= Adjustments::empty($money->getCurrency(), $money->getContext());
        }

        return $this->adjustments = $this->adjust($money);
    }

    public function locate(BackedEnum|UnitEnum|string $code): ?Adjustment
    {
        return $this->adjustments?->locate($code);
    }

    public function remove(BackedEnum|UnitEnum|string $code): void
    {
        $this->items = $this->filter(static fn(Adjuster $adjuster) => $adjuster->code() !== $code);
        $this->forceRecalculation();
    }

    public function create(Adjuster $adjuster): void
    {
        $this->items[] = $adjuster;
        $this->forceRecalculation();
    }

    protected function type(): string
    {
        return Adjuster::class;
    }

    protected function adjust(Money $money): Adjustments
    {
        return Adjustments::from(
            $money->getCurrency(),
            $money->getContext(),
            $this->map($this->createAdjustment($money))
        );
    }

    protected function createAdjustment(Money $money): callable
    {
        return static fn(Adjuster $adjuster) => new Adjustment(
            $adjuster->adjust($money), $adjuster->type(), $adjuster->value(), $adjuster->code()
        );
    }

    private function needsRecalculation(Money $money): bool
    {
        $current = $money->getAmount()->toBigDecimal();

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
