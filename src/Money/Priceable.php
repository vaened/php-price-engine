<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use Brick\Money\Money;

interface Priceable extends Taxable
{
    public function value(): Money;
}