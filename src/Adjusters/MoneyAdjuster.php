<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

use Brick\Money\Money;

interface MoneyAdjuster extends AdjusterScheme
{
    public function adjust(Money $money): Money;
}