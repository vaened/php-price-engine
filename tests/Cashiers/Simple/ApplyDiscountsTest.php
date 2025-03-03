<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Taxation;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class ApplyDiscountsTest extends SimpleCashierTestCase
{
    public function test_apply_discount_recalculate_all_totals(): void
    {
        $this->cashier->apply(
            Discount::proportional(3)->named('TESTING-3%'),
            Discount::proportional(7)->named('TESTING-7%'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(100.0000),
                subtotal     : self::money(1000.0000),
                totalTaxes   : self::money(173.5540),
                totalCharges : self::money(51.3220),
                totaDiscounts: self::money(104.1730),
                total        : self::money(947.1490),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(173.5540, Taxation\Inclusive::proportional(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(41.3220, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::fixed(10)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5290, Discount::proportional(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::fixed(5)->named(DiscountCode::Promotional)),
            self::createAdjustment(24.7930, Discount::proportional(3)->named('TESTING-3%')),
            self::createAdjustment(57.8510, Discount::proportional(7)->named('TESTING-7%')),
        );
    }
}
