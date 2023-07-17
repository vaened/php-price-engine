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

final class InitialCalculationsTest extends StandardCashierTestCase
{
    public function test_initial_calculations_are_correct(): void
    {
        $this->assertTotals(
            Summary::is(
                quantity     : 10,
                unitPrice    : self::money(82.6446),
                subtotal     : self::money(826.4460),
                totalTaxes   : self::money(173.5537),
                totalCharges : self::money(51.3223),
                totaDiscounts: self::money(21.5289),
                total        : self::money(1029.7931),
            )
        );

        $this->assertTaxes(
            self::createAdjustment(173.5537, Tax\Inclusive::percentagely(21, TaxCode::IVA)),
        );

        $this->assertCharges(
            self::createAdjustment(41.3223, Charge::percentagely(5)->named(ChargeCode::POS)),
            self::createAdjustment(10.0, Charge::uniformly(10)->named(ChargeCode::Delivery)),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5289, Discount::percentagely(2)->named(DiscountCode::NewUsers)),
            self::createAdjustment(5.0, Discount::uniformly(5)->named(DiscountCode::Promotional)),
        );
    }
}
