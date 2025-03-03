<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Taxation;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Helper;

use function count;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\some;

final class TaxCodes
{
    public const ANY = ['*'];

    private readonly array $codes;

    public function __construct(iterable $codes)
    {
        $this->codes = map($this->stringify(), $codes);
    }

    public static function any(): self
    {
        return new self(self::ANY);
    }

    public static function none(): self
    {
        return new self([]);
    }

    public static function only(iterable $codes): self
    {
        return new self($codes);
    }

    public function values(): array
    {
        return $this->codes;
    }

    public function isValid(BackedEnum|UnitEnum|string $code): bool
    {
        return $this->isAnyAllowed()
            || some(
                static fn(string $item) => $item === Helper::processEnumerableCode($code),
                $this->codes
            );
    }

    public function isNothingAllowed(): bool
    {
        return count($this->codes) === 0;
    }

    public function isAnyAllowed(): bool
    {
        return $this->codes === self::ANY;
    }

    private function stringify(): callable
    {
        return static fn(mixed $code) => Helper::processEnumerableCode($code) ?? throw new InvalidTaxEncoding();
    }
}
