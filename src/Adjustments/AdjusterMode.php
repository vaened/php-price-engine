<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

enum AdjusterMode: string
{
    case PerUnit = 'UNI';

    case ForTotal = 'TOT';
}
