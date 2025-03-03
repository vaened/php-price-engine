<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjustmentMode, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Taxation;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class UpdateQuantityTest extends RegularCashierTestCase
{
    public function test_change_quantity_recalculates_all_totals(): void
    {
        $this->cashier->update(quantity: 3);

        $this->assertTotals(
            Summary::is(
                quantity     : 3,
                unitPrice    : self::money(99.9999),
                subtotal     : self::money(299.9997),
                totalTaxes   : self::money(50.8473),
                totalCharges : self::money(21.0),
                totaDiscounts: self::money(6.0),
                total        : self::money(314.9997),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(44.8473, Taxation\Inclusive::proportional(18, TaxCode::IGV)),
            self::createAdjustment(6.0, Taxation\Inclusive::fixed(2, TaxCode::ISC)),
        );

        $this->assertCharges(
            self::createAdjustment(15.0, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(6.0, Charge::fixed(2, AdjustmentMode::PerUnit)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(6.0, Discount::proportional(2)->named(DiscountCode::NewUsers)),
        );
    }
}
