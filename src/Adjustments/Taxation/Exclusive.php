<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Taxation;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\AdjustmentMode;
use Vaened\PriceEngine\Adjustments\AdjustmentType;

final class Exclusive extends TaxScheme
{
    public static function proportional(int $percentage, BackedEnum|UnitEnum|string $code): self
    {
        return new self(AdjustmentType::Percentage, $percentage, AdjustmentMode::PerUnit, $code);
    }

    public static function fixed(float $amount, BackedEnum|UnitEnum|string $code, AdjustmentMode $mode = AdjustmentMode::PerUnit): self
    {
        return new self(AdjustmentType::Uniform, $amount, $mode, $code);
    }

    public function isInclusive(): bool
    {
        return false;
    }
}
