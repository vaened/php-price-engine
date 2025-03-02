<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Tax;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\AdjusterMode;
use Vaened\PriceEngine\Adjustments\AdjusterType;

final class Inclusive extends Taxation
{
    public static function proportional(int $percentage, BackedEnum|UnitEnum|string $code): self
    {
        return new self(AdjusterType::Percentage, $percentage, AdjusterMode::PerUnit, $code);
    }

    public static function fixed(float $amount, BackedEnum|UnitEnum|string $code, AdjusterMode $mode = AdjusterMode::PerUnit): self
    {
        return new self(AdjusterType::Uniform, $amount, $mode, $code);
    }

    public function isInclusive(): bool
    {
        return true;
    }
}
