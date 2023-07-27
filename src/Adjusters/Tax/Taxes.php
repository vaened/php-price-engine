<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters\Tax;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjusters\Adjusters;
use Vaened\PriceEngine\Calculators\InclusiveAdjustmentHandler;
use Vaened\Support\Types\ArrayObject;

use function in_array;

final class Taxes extends ArrayObject
{
    public static function from(array $items): self
    {
        return new self($items);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function toAdjusters(): Adjusters
    {
        return Adjusters::from(
            $this->map($this->toCharge())
        );
    }

    public function additionally(self $taxes): self
    {
        return self::from([
            ...$this->items(),
            ...$taxes->items()
        ]);
    }

    public function onlyAdjustablesOf(TaxCodes $allowed): self
    {
        return match (true) {
            $allowed->isNothingAllowed() => self::empty(),
            $allowed->isAnyAllowed() => $this,
            default => $this->filter($this->allowed($allowed->values()))
        };
    }

    public function clean(Money $money): Money
    {
        return $this->filter($this->inclusives())
                    ->reduce($this->breakdown(), $money);
    }

    protected function type(): string
    {
        return Taxation::class;
    }

    private function inclusives(): callable
    {
        return static fn(Taxation $taxation) => $taxation->isInclusive();
    }

    private function breakdown(): callable
    {
        return static fn(
            Money $money, Taxation $taxation
        ) => InclusiveAdjustmentHandler::apply($money, $taxation);
    }

    private function toCharge(): callable
    {
        return static fn(Taxation $taxation) => $taxation->toCharge();
    }

    private function allowed(array $codes): callable
    {
        return static fn(Taxation $taxation) => in_array($taxation->code(), $codes, true);
    }
}
