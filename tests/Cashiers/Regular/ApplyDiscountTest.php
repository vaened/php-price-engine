<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjustmentMode, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Taxation;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class ApplyDiscountTest extends RegularCashierTestCase
{
    public function test_apply_discount_recalculate_all_totals(): void
    {
        $this->cashier->apply(
            Discount::proportional(3)->named('TESTING-3%'),
            Discount::proportional(7)->named('TESTING-7%'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 6,
                unitPrice    : self::money(99.9999),
                subtotal     : self::money(599.9994),
                totalTaxes   : self::money(101.6946),
                totalCharges : self::money(42.0),
                totaDiscounts: self::money(72.0),
                total        : self::money(569.9994),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(89.6946, Taxation\Inclusive::proportional(18, TaxCode::IGV)),
            self::createAdjustment(12, Taxation\Inclusive::fixed(2, TaxCode::ISC)),
        );

        $this->assertCharges(
            self::createAdjustment(30.0, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(12.0, Charge::fixed(2, AdjustmentMode::PerUnit)->named(ChargeCode::Delivery)),
        );
        $this->assertDiscounts(
            self::createAdjustment(12.0, Discount::proportional(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(18.0, Discount::proportional(3)->named('TESTING-3%')),
            self::createAdjustment(42.0, Discount::proportional(7)->named('TESTING-7%')),
        );
    }
}
