<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Standard;

use Vaened\PriceEngine\Adjusters\Adjusters;
use Vaened\PriceEngine\Adjusters\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjusters\Tax;
use Vaened\PriceEngine\Calculators\StandardCashier;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Money\Charge;
use Vaened\PriceEngine\Money\Discount;
use Vaened\PriceEngine\Tests\Cashiers\CashierTestCase;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

abstract class StandardCashierTestCase extends CashierTestCase
{
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
