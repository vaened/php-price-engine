<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters\Tax;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\AdjusterScheme;
use Vaened\PriceEngine\Adjusters\AdjusterType;
use Vaened\PriceEngine\Helper;
use Vaened\PriceEngine\Money\Charge;

abstract class Taxation implements AdjusterScheme
{
    private readonly string $code;

    protected function __construct(
        private readonly AdjusterType $type,
        private readonly float|int    $value,
        BackedEnum|UnitEnum|string    $code
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

    public function value(): float|int
    {
        return $this->value;
    }

    public function toCharge(): Charge
    {
        return new Charge($this->type, $this->value, $this->code);
    }
}
