<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

use BackedEnum;
use Brick\Math\RoundingMode;
use ReflectionClass;
use UnitEnum;
use Vaened\PriceEngine\PriceEngineConfig;
use Vaened\PriceEngine\Helper;

use function sprintf;
use function uniqid;

class Adjustment implements AdjustmentScheme
{
    private ?string $code;

    public function __construct(
        private readonly AdjustmentType $type,
        private readonly int|float      $value,
        private readonly AdjustmentMode $mode,
        BackedEnum|UnitEnum|string|null $code = null,
    )
    {
        $this->code = Helper::processEnumerableCode($code);
    }

    public static function proportional(int $percentage): static
    {
        return new static(AdjustmentType::Percentage, $percentage, AdjustmentMode::PerUnit);
    }

    public static function fixed(float $amount, AdjustmentMode $mode = AdjustmentMode::ForTotal): static
    {
        return new static(AdjustmentType::Uniform, $amount, $mode);
    }

    public function named(BackedEnum|UnitEnum|string $code): static
    {
        $this->code = Helper::processEnumerableCode($code);
        return $this;
    }

    public function code(): string
    {
        return $this->code ??= uniqid(sprintf('%s_', (new ReflectionClass($this))->getShortName()), true);
    }

    public function type(): AdjustmentType
    {
        return $this->type;
    }

    public function mode(): AdjustmentMode
    {
        return $this->mode;
    }

    public function value(): float|int
    {
        return $this->value;
    }

    protected function roundingMode(): RoundingMode
    {
        return PriceEngineConfig::defaultRoundingMode();
    }
}