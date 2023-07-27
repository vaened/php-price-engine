<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Math\RoundingMode;
use UnitEnum;

use function is_string;

final class Helper
{
    private static int $roundingMode = RoundingMode::HALF_EVEN;

    public static function setDefaultRoundingMode(int $roundingMode): void
    {
        self::$roundingMode = $roundingMode;
    }

    public static function defaultRoundingMode(): int
    {
        return self::$roundingMode;
    }

    public static function processEnumerableCode(BackedEnum|UnitEnum|string|null $code): string|int|null
    {
        return match (true) {
            null === $code || is_string($code) => $code,
            $code instanceof BackedEnum => $code->value,
            $code instanceof UnitEnum => $code->name,
        };
    }

    public static function percentageize(int $percentage): float
    {
        return $percentage / 100;
    }
}
