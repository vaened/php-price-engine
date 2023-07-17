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
    private static Calculator $calculator;
    private static int        $roundingMode = RoundingMode::HALF_UP;

    public static function setCalculator(Calculator $calculator): void
    {
        self::$calculator = $calculator;
    }

    public static function calculator(): Calculator
    {
        return self::$calculator ??= new Calculator();
    }

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
            $code instanceof BackedEnum        => $code->value,
            $code instanceof UnitEnum          => $code->name,
        };
    }
}
