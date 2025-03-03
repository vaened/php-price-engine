<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjusterMode;
use Vaened\PriceEngine\Adjustments\AdjusterScheme;
use Vaened\PriceEngine\Adjustments\AdjusterType;

final class Modifier implements AdjusterScheme
{
    public function __construct(
        private readonly Money        $amount,
        private readonly AdjusterType $type,
        private readonly AdjusterMode $mode,
        private readonly int|float    $value,
        private readonly string       $code
    )
    {
    }

    public function amount(): Money
    {
        return $this->amount;
    }

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

    public function mode(): AdjusterMode
    {
        return $this->mode;
    }
}
