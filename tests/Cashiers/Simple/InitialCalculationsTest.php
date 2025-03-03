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

final class InitialCalculationsTest extends SimpleCashierTestCase
{
    public function test_initial_calculations_are_correct(): void
    {
        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(100.0000),
                subtotal     : self::money(1000.0000),
                totalTaxes   : self::money(173.5540),
                totalCharges : self::money(51.3220),
                totaDiscounts: self::money(21.5290),
                total        : self::money(1029.7930),
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
        );
    }
}
