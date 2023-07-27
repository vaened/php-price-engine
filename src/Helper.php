<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use UnitEnum;

use function is_string;

final class Helper
{
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
