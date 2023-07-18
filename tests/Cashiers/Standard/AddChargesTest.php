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

final class AddChargesTest extends StandardCashierTestCase
{
    public function test_add_charges_recalculate_all_totals(): void
    {
        $this->cashier->add(
            Charge::percentagely(12)->named('TESTING-12%'),
            Charge::uniformly(20)->named('TESTING-20'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(82.6446),
                subtotal     : self::money(826.4460),
                totalTaxes   : self::money(173.5537),
                totalCharges : self::money(170.4958),
                totaDiscounts: self::money(21.5289),
                total        : self::money(1148.9666),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(173.5537, Tax\Inclusive::percentagely(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(41.3223, Charge::percentagely(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::uniformly(10)->named(ChargeCode::Delivery)),
            self::createAdjustment(99.1735, Charge::percentagely(12)->named('TESTING-12%')),
            self::createAdjustment(20.0, Charge::uniformly(20)->named('TESTING-20')),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5289, Discount::percentagely(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::uniformly(5)->named(DiscountCode::Promotional)),
        );
    }
}
