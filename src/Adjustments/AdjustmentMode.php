<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

enum AdjustmentMode: string
{
    case PerUnit = 'UNI';

    case ForTotal = 'TOT';
}
