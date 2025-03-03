<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class UpdateQuantityTest extends SimpleCashierTestCase
{
    public function test_change_quantity_recalculates_all_totals(): void
    {
        $this->cashier->update(quantity: 3);
        $this->assertTotals(
            Summary::is(
                quantity     : 3,
                unitPrice    : self::money(100.0000),
                subtotal     : self::money(300.0000),
                totalTaxes   : self::money(52.0662),
                totalCharges : self::money(22.3966),
                totaDiscounts: self::money(9.9587),
                total        : self::money(312.4379),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(52.0662, Tax\Inclusive::proportional(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(12.3966, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::fixed(10)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(4.9587, Discount::proportional(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::fixed(5)->named(DiscountCode::Promotional)),
        );
    }
}
