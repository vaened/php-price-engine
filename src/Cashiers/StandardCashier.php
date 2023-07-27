<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\Adjusters;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Prices\Price;
use Vaened\PriceEngine\Money\Prices\RegularPrice;

final class StandardCashier extends Cashier
{
    protected function createUnitPrice(
        Money     $unitPrice,
        Adjusters $taxes,
        Adjusters $discounts,
        Adjusters $charges
    ): Price
    {
        return new RegularPrice($unitPrice);
    }
}
