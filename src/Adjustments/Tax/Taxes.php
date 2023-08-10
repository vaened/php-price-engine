<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Tax;

use Vaened\PriceEngine\Adjustments\Adjusters;
use Vaened\Support\Types\TypedList;

use function in_array;

final class Taxes extends TypedList
{
    public static function from(iterable $items): self
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

    protected function type(): string
    {
        return Taxation::class;
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
