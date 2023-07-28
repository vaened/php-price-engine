<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\Adjusters;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Prices\FlatPrice;
use Vaened\PriceEngine\Money\Prices\Price;

/**
 * The SimpleCashier class represents a cashier that calculates prices based on the gross price without taxes.
 *
 * This cashier applies discounts and charges directly to the base price before adding taxes, resulting in a
 * simple and straightforward price calculation.
 */
final class SimpleCashier extends Cashier
{
    protected function createUnitPrice(Money $grossUnitPrice, Adjusters $taxes): Price
    {
        return new FlatPrice($grossUnitPrice, $grossUnitPrice, $grossUnitPrice);
    }
}
