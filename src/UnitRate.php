<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;

interface UnitRate
{
    /**
     * Obtain the amount eligible for discounts.
     *
     * This amount represents the unit amount applicable to discounts, which,
     * in general, is the final price that the customer will see, showing any
     * reduction through discounts or promotions.
     *
     * @return Money The discountable amount as the Money object.
     */
    public function discountable(): Money;

    /**
     * Obtain the amount subject to taxes.
     *
     * This amount represents the unit amount applicable to taxes, that is, the
     * gross price in most cases.
     *
     * @return Money The amount taxable as the Money object.
     */
    public function taxable(): Money;

    /**
     * Obtain the amount subject to charges.
     *
     * This amount is the unit price to which additional charges or fees may be added.
     *
     * @return Money The chargeable amount as a Money object.
     */
    public function chargeable(): Money;
}