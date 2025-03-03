<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Taxation;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjustments\AdjustmentMode;
use Vaened\PriceEngine\Adjustments\AdjustmentScheme;
use Vaened\PriceEngine\Adjustments\AdjustmentType;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Helper;

abstract class TaxScheme implements AdjustmentScheme
{
    private readonly string $code;

    protected function __construct(
        private readonly AdjustmentType $type,
        private readonly float|int      $value,
        private readonly AdjustmentMode $mode,
        BackedEnum|UnitEnum|string      $code,
    )
    {
        $this->code = Helper::processEnumerableCode($code);
    }

    abstract public function isInclusive(): bool;

    public function code(): string
    {
        return $this->code;
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

    public function toCharge(): Charge
    {
        return new Charge($this->type, $this->value, $this->mode, $this->code);
    }
}
