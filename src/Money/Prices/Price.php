<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money\Prices;

use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;

interface Price
{
    public function discountable(): Money;

    public function taxable(): Money;

    public function chargeable(): Money;

    public function currency(): Currency;

    public function context(): Context;
}