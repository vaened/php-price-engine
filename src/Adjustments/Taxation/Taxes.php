<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Taxation;

use Vaened\PriceEngine\Adjustments\Adjustments;
use Vaened\Support\Types\SecureList;

use function in_array;

final class Taxes extends SecureList
{
    public static function from(iterable $items): self
    {
        return new self($items);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function toAdjustments(): Adjustments
    {
        return Adjustments::from(
            $this->map($this->toCharge())
        );
    }

    public function only(TaxCodes $allowed): self
    {
        return match (true) {
            $allowed->isNothingAllowed() => self::empty(),
            $allowed->isAnyAllowed() => $this,
            default => $this->filter($this->allowed($allowed->values()))
        };
    }

    private function toCharge(): callable
    {
        return static fn(TaxScheme $taxation) => $taxation->toCharge();
    }

    private function allowed(array $codes): callable
    {
        return static fn(TaxScheme $taxation) => in_array($taxation->code(), $codes, true);
    }

    public static function type(): string
    {
        return TaxScheme::class;
    }
}
