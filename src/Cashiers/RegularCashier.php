<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Cashiers;

use Vaened\PriceEngine\{Price, UnitRate};
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Cashiers\Rates\ManualUnitRate;

/**
 * The RegularCashier class represents a cashier that calculates prices based on the price with tax included.
 *
 * This cashier applies discounts and fees to the total price with tax, providing a standard price calculation
 * commonly attractive approach to clients.
 */
final class RegularCashier extends Cashier
{
    protected function createUnitRate(Price $price): UnitRate
    {
        return new ManualUnitRate($price->net(), $price->net(), $price->gross());
    }
}
