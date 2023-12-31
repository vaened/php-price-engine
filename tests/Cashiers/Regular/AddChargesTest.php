<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjusterMode, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class AddChargesTest extends RegularCashierTestCase
{
    public function test_add_charges_recalculate_all_totals(): void
    {
        $this->cashier->add(
            $testing12Charge = Charge::proporcional(12)->named('TESTING-12%'),
            $testing20Charge = Charge::fixed(20, AdjusterMode::PerUnit)->named('TESTING-20'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 6,
                unitPrice    : self::money(83.0508),
                subtotal     : self::money(498.3048),
                totalTaxes   : self::money(101.6946),
                totalCharges : self::money(234.0),
                totaDiscounts: self::money(12.0),
                total        : self::money(821.9994),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(89.6946, Tax\Inclusive::proporcional(18, TaxCode::IGV)),
            self::createAdjustment(12.0, Tax\Inclusive::fixed(2, TaxCode::ISC)),
        );

        $this->assertCharges(
            self::createAdjustment(30.0, Charge::proporcional(5)->named(ChargeCode::POS)),
            self::createAdjustment(12, Charge::fixed(2, AdjusterMode::PerUnit)->named(ChargeCode::Delivery)),
            self::createAdjustment(72.0, $testing12Charge),
            self::createAdjustment(120.0, $testing20Charge),
        );

        $this->assertDiscounts(
            self::createAdjustment(12.0, Discount::proporcional(2)->named(DiscountCode::NewUsers)),
        );
    }
}
