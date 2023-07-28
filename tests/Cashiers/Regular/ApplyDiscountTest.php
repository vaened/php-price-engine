<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjusterMode, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class ApplyDiscountTest extends RegularCashierTestCase
{
    public function test_apply_discount_recalculate_all_totals(): void
    {
        $this->cashier->apply(
            Discount::proporcional(3)->named('TESTING-3%'),
            Discount::proporcional(7)->named('TESTING-7%'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 6,
                unitPrice    : self::money(83.0508),
                subtotal     : self::money(498.3048),
                totalTaxes   : self::money(101.6946),
                totalCharges : self::money(42.0),
                totaDiscounts: self::money(72.0),
                total        : self::money(569.9994),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(89.6946, Tax\Inclusive::proporcional(18, TaxCode::IGV)),
            self::createAdjustment(12, Tax\Inclusive::fixed(2, TaxCode::ISC)),
        );

        $this->assertCharges(
            self::createAdjustment(30.0, Charge::proporcional(5)->named(ChargeCode::POS)),
            self::createAdjustment(12.0, Charge::fixed(2, AdjusterMode::PerUnit)->named(ChargeCode::Delivery)),
        );
        $this->assertDiscounts(
            self::createAdjustment(12.0, Discount::proporcional(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(18.0, Discount::proporcional(3)->named('TESTING-3%')),
            self::createAdjustment(42.0, Discount::proporcional(7)->named('TESTING-7%')),
        );
    }
}
