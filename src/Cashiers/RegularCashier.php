<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\{UnitRate};
use Vaened\PriceEngine\AdjustmentManager;
use Vaened\PriceEngine\Adjustments\Adjusters;
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
    private const ONE = 1;

    protected function createUnitRate(Money $grossUnitPrice, Adjusters $taxes): UnitRate
    {
        $unitPriceIncludingTaxes = $grossUnitPrice->plus(
            (new AdjustmentManager($taxes, $grossUnitPrice, self::ONE))->total()
        );

        return new ManualUnitRate($unitPriceIncludingTaxes, $unitPriceIncludingTaxes, $grossUnitPrice);
    }
}
