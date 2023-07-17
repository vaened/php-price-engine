<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Standard;

use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use Vaened\PriceEngine\Adjusters\Adjusters;
use Vaened\PriceEngine\Adjusters\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjusters\Tax;
use Vaened\PriceEngine\Calculators\StandardCashier;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Money\Charge;
use Vaened\PriceEngine\Money\Discount;
use Vaened\PriceEngine\Tests\Cashiers\CashierTestCase;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class ComplexCashierTest extends CashierTestCase
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
            self::createAdjustment(41.3223, Charge::percentagely(5)->named('POS')),
            self::createAdjustment(10.0, Charge::uniformly(10)->named('RANDOM')),
        );

        $this->assertDiscounts(
            self::createAdjustment(16.5289, Discount::percentagely(2)->named('NEW_USERS')),
            self::createAdjustment(5.0, Discount::uniformly(5)->named('PROMOTIONAL')),
        );
    }

    protected function cashier(): Cashier
    {
        return new StandardCashier(
            Amount::taxable(
                self::money(100),
                TaxCodes::only([TaxCode::IVA])
            ),
            quantity   : 10,
            taxes      : Taxes::from([
                Tax\Inclusive::percentagely(21, TaxCode::IVA),
                Tax\Inclusive::percentagely(18, TaxCode::IGV),
            ]),
            charges    : Adjusters::from([
                Charge::percentagely(5)->named('POS'),
                Charge::uniformly(10)->named('RANDOM'),
            ]),
            subtractors: Adjusters::from([
                Discount::percentagely(2)->named('NEW_USERS'),
                Discount::uniformly(5)->named('PROMOTIONAL'),
            ])
        );
    }
}
