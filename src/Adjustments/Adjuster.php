<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

use BackedEnum;
use ReflectionClass;
use UnitEnum;
use Vaened\PriceEngine\Config;
use Vaened\PriceEngine\Helper;

use function sprintf;
use function uniqid;

class Adjuster implements AdjusterScheme
{
    private ?string $code;

    public function __construct(
        private readonly AdjusterType   $type,
        private readonly int|float      $value,
        private readonly AdjusterMode   $mode,
        BackedEnum|UnitEnum|string|null $code = null,
    )
    {
        $this->code = Helper::processEnumerableCode($code);
    }

    public static function proporcional(int $percentage): static
    {
        return new static(AdjusterType::Percentage, $percentage, AdjusterMode::PerUnit);
    }

    public static function fixed(float $amount, AdjusterMode $mode = AdjusterMode::ForTotal): static
    {
        return new static(AdjusterType::Uniform, $amount, $mode);
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

    public function type(): AdjusterType
    {
        return $this->type;
    }

    public function mode(): AdjusterMode
    {
        return $this->mode;
    }

    public function value(): float|int
    {
        return $this->value;
    }

    protected function roundingMode(): int
    {
        return Config::defaultRoundingMode();
    }
}