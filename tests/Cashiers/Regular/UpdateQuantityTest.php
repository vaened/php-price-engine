<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjusterMode, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class UpdateQuantityTest extends RegularCashierTestCase
{
    public function test_change_quantity_recalculates_all_totals(): void
    {
        $this->cashier->update(quantity: 3);

        $this->assertTotals(
            Summary::is(
                quantity     : 3,
                unitPrice    : self::money(83.0508),
                subtotal     : self::money(249.1524),
                totalTaxes   : self::money(50.8473),
                totalCharges : self::money(21.0),
                totaDiscounts: self::money(6.0),
                total        : self::money(314.9997),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(44.8473, Tax\Inclusive::proportional(18, TaxCode::IGV)),
            self::createAdjustment(6.0, Tax\Inclusive::fixed(2, TaxCode::ISC)),
        );

        $this->assertCharges(
            self::createAdjustment(15.0, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(6.0, Charge::fixed(2, AdjusterMode::PerUnit)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(6.0, Discount::proportional(2)->named(DiscountCode::NewUsers)),
        );
    }
}
