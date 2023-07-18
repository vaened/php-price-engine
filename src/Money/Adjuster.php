<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use BackedEnum;
use Brick\Money\Money;
use ReflectionClass;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\AdjusterType;
use Vaened\PriceEngine\Adjusters\MoneyAdjuster;
use Vaened\PriceEngine\Helper;
use function sprintf;
use function uniqid;

class Adjuster implements MoneyAdjuster
{
    private ?string $code;

    public function __construct(
        private readonly AdjusterType   $type,
        private readonly int|float      $value,
        BackedEnum|UnitEnum|string|null $code = null
    )
    {
        $this->code = Helper::processEnumerableCode($code);
    }

    public static function proporcional(int $percentage): static
    {
        return new static(AdjusterType::Percentage, $percentage);
    }

    public static function fixed(float $amount): static
    {
        return new static(AdjusterType::Uniform, $amount);
    }

    public function adjust(Money $money): Money
    {
        return Helper::calculator()->byExclusive($money, $this->type(), $this->value());
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

    public function value(): float|int
    {
        return $this->value;
    }

    protected function roundingMode(): int
    {
        return Helper::defaultRoundingMode();
    }
}