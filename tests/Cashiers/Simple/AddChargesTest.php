<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\AdjusterMode;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class AddChargesTest extends SimpleCashierTestCase
{
    public function test_add_charges_recalculate_all_totals(): void
    {
        $this->cashier->add(
            $testing12Discount = Charge::proportional(12)->named('TESTING-12%'),
            $testing20Discount = Charge::fixed(20, AdjusterMode::PerUnit)->named('TESTING-20'),
        );

        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(100.0),
                subtotal     : self::money(1000.0),
                totalTaxes   : self::money(173.5540),
                totalCharges : self::money(350.4960),
                totaDiscounts: self::money(21.5290),
                total        : self::money(1328.9670),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(173.5540, Tax\Inclusive::proportional(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(41.3220, Charge::proportional(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::fixed(10)->named(ChargeCode::Delivery)),
            self::createAdjustment(99.1740, $testing12Discount),
            self::createAdjustment(200.0, $testing20Discount),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5290, Discount::proportional(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::fixed(5)->named(DiscountCode::Promotional)),
        );
    }
}
