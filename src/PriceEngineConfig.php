<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Math\RoundingMode;

final class PriceEngineConfig
{
    private static RoundingMode $roundingMode = RoundingMode::HALF_EVEN;

    public static function setDefaultRoundingMode(RoundingMode $roundingMode): void
    {
        self::$roundingMode = $roundingMode;
    }

    public static function defaultRoundingMode(): RoundingMode
    {
        return self::$roundingMode;
    }
}
