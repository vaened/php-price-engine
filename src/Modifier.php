<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjustmentMode;
use Vaened\PriceEngine\Adjustments\AdjustmentScheme;
use Vaened\PriceEngine\Adjustments\AdjustmentType;

final class Modifier implements AdjustmentScheme
{
    public function __construct(
        private readonly Money          $amount,
        private readonly AdjustmentType $type,
        private readonly AdjustmentMode $mode,
        private readonly int|float      $value,
        private readonly string         $code
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

    public function type(): AdjustmentType
    {
        return $this->type;
    }

    public function value(): float|int
    {
        return $this->value;
    }

    public function mode(): AdjustmentMode
    {
        return $this->mode;
    }
}
