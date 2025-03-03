<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;
use UnitEnum;
use Vaened\Support\Types\SecureList;

use function Lambdish\Phunctional\reduce;

final class Modifiers extends SecureList
{
    private readonly Money $total;

    public function __construct(iterable $items, Currency $moneyCurrency, Context $moneyContext)
    {
        parent::__construct($items);
        $this->sumTotals(initial: Money::zero($moneyCurrency, $moneyContext));
    }

    public function locate(BackedEnum|UnitEnum|string $code): ?Modifier
    {
        return $this->items[Helper::processEnumerableCode($code)] ?? null;
    }

    public function total(): Money
    {
        return $this->total;
    }

    protected function safelyProcessItems(iterable $items): array
    {
        return reduce(static function (array $acc, Modifier $adjustment) {
            $acc[$adjustment->code()] = $adjustment;
            return $acc;
        }, $items, initial: []);
    }

    private function sumTotals(Money $initial): void
    {
        $this->total = $this->reduce(
            static fn(?Money $acc, Modifier $adjustment) => null === $acc
                ? $adjustment->amount()
                : $acc->plus($adjustment->amount()),
            $initial
        );
    }

    public static function type(): string
    {
        return Modifier::class;
    }
}
