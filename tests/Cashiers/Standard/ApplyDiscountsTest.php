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

final class ApplyDiscountsTest extends StandardCashierTestCase
{
    public function test_apply_discount_recalculate_all_totals(): void
    {
        $this->cashier->apply(
            Discount::percentagely(3)->named('TESTING-3%'),
            Discount::percentagely(7)->named('TESTING-7%'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(82.6446),
                subtotal     : self::money(826.4460),
                totalTaxes   : self::money(173.5537),
                totalCharges : self::money(51.3223),
                totaDiscounts: self::money(104.1735),
                total        : self::money(947.1485),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(173.5537, Tax\Inclusive::percentagely(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(41.3223, Charge::percentagely(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::uniformly(10)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5289, Discount::percentagely(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::uniformly(5)->named(DiscountCode::Promotional)),
            self::createAdjustment(24.7934, Discount::percentagely(3)->named('TESTING-3%')),
            self::createAdjustment(57.8512, Discount::percentagely(7)->named('TESTING-7%')),
        );
    }
}
