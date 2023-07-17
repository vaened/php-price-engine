<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

interface AdjusterScheme
{
    public function code(): string;

    public function type(): AdjusterType;

    public function value(): float|int;
}