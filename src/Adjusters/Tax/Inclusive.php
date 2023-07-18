<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters\Tax;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\AdjusterType;

final class Inclusive extends Taxation
{
    public static function proporcional(int $percentage, BackedEnum|UnitEnum|string $code): self
    {
        return new self(AdjusterType::Percentage, $percentage, $code);
    }

    public static function fixed(float $amount, BackedEnum|UnitEnum|string $code): self
    {
        return new self(AdjusterType::Uniform, $amount, $code);
    }

    public function isInclusive(): bool
    {
        return true;
    }
}
