<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

enum AdjusterType: string
{
    case Percentage = 'PCT';

    case Uniform = 'FIX';
}