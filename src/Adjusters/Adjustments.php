<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

use ArrayIterator;
use BackedEnum;
use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;
use Countable;
use IteratorAggregate;
use Traversable;
use UnitEnum;
use Vaened\PriceEngine\Helper;
use Vaened\Support\Types\InvalidType;
use function count;
use function Lambdish\Phunctional\any;
use function Lambdish\Phunctional\reduce;

final class Adjustments implements Countable, IteratorAggregate
{
    private readonly Money $total;

    public function __construct(Currency $currency, Context $context, private iterable $items)
    {
        $this->ensureType($items);
        $this->indexItems();
        $this->sumTotals(initial: Money::zero($currency, $context));
    }

    private function ensureType(iterable $items): void
    {
        any(
            fn(mixed $item) => $item instanceof Adjustment ?: throw new InvalidType(self::class, Adjustment::class, $item::class),
            $items
        );
    }

    private function indexItems(): void
    {
        $this->items = reduce(static function (array $acc, Adjustment $adjustment) {
            $acc[$adjustment->code()] = $adjustment;
            return $acc;
        }, $this->items, []);
    }

    private function sumTotals(Money $initial): void
    {
        $this->total = reduce(static fn(?Money $acc, Adjustment $adjustment) => null === $acc
            ? $adjustment->amount()
            : $acc->plus($adjustment->amount()), $this->items, $initial);
    }

    public static function from(Currency $currency, Context $context, iterable $items): self
    {
        return new self($currency, $context, $items);
    }

    public static function empty(Currency $currency, Context $context): self
    {
        return new self($currency, $context, []);
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
}
