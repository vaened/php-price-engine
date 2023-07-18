<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Standard;

use Vaened\PriceEngine\Adjusters\Tax;
use Vaened\PriceEngine\Money\Charge;
use Vaened\PriceEngine\Money\Discount;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class UpdateQuantityTest extends StandardCashierTestCase
{
    public function test_change_quantity_recalculates_all_totals(): void
    {
        $this->cashier->update(quantity: 3);

        $this->assertTotals(
            Summary::is(
                quantity     : 3,
                unitPrice    : self::money(82.6446),
                subtotal     : self::money(247.9338),
                totalTaxes   : self::money(52.0661),
                totalCharges : self::money(22.3967),
                totaDiscounts: self::money(9.9587),
                total        : self::money(312.4379),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(52.0661, Tax\Inclusive::percentagely(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(12.3967, Charge::percentagely(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::uniformly(10)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(4.9587, Discount::percentagely(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::uniformly(5)->named(DiscountCode::Promotional)),
        );
    }
}