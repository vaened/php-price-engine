<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use ArrayIterator;
use BackedEnum;
use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;
use Countable;
use IteratorAggregate;
use Traversable;
use UnitEnum;
use Vaened\Support\Types\InvalidType;

use function count;
use function Lambdish\Phunctional\any;
use function Lambdish\Phunctional\reduce;

final class Adjustments implements Countable, IteratorAggregate
{
    private readonly Money $total;

    private array          $items;

    public function __construct(array $items, Currency $moneyCurrency, Context $moneyContext)
    {
        $this->ensureType($items);
        $this->indexItems($items);
        $this->sumTotals(initial: Money::zero($moneyCurrency, $moneyContext));
    }

    public function locate(BackedEnum|UnitEnum|string $code): ?Adjustment
    {
        return $this->items[Helper::processEnumerableCode($code)] ?? null;
    }

    public function total(): Money
    {
        return $this->total;
    }

    public function items(): iterable
    {
        return $this->items;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    private function ensureType(iterable $items): void
    {
        any(
            fn(mixed $item) => $item instanceof Adjustment ?: throw new InvalidType(self::class, Adjustment::class, $item::class),
            $items
        );
    }

    private function indexItems(array $items): void
    {
        $this->items = reduce(static function (array $acc, Adjustment $adjustment) {
            $acc[$adjustment->code()] = $adjustment;
            return $acc;
        }, $items, []);
    }

    private function sumTotals(Money $initial): void
    {
        $this->total = reduce(
            static fn(?Money $acc, Adjustment $adjustment) => null === $acc
                ? $adjustment->amount()
                : $acc->plus($adjustment->amount()),
            $this->items,
            $initial
        );
    }
}
