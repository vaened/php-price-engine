<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Tax;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\AdjusterMode;
use Vaened\PriceEngine\Adjustments\AdjusterScheme;
use Vaened\PriceEngine\Adjustments\AdjusterType;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Helper;

abstract class Taxation implements AdjusterScheme
{
    private readonly string $code;

    protected function __construct(
        private readonly AdjusterType $type,
        private readonly float|int    $value,
        private readonly AdjusterMode $mode,
        BackedEnum|UnitEnum|string    $code,
    )
    {
        $this->code = Helper::processEnumerableCode($code);
    }

    abstract public function isInclusive(): bool;

    public function code(): string
    {
        return $this->code;
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

    public function toCharge(): Charge
    {
        return new Charge($this->type, $this->value, $this->mode, $this->code);
    }
}
