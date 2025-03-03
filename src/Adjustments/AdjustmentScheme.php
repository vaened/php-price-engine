<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

interface AdjustmentScheme
{
    public function code(): string;

    public function type(): AdjustmentType;

    public function mode(): AdjustmentMode;

    public function value(): float|int;
}