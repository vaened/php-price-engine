<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\Adjusters;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Cashiers\SimpleCashier;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Tests\Cashiers\CashierTestCase;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

abstract class SimpleCashierTestCase extends CashierTestCase
{
    protected function cashier(): Cashier
    {
        return new SimpleCashier(
            Amount::taxable(
                self::money(100),
                TaxCodes::only([TaxCode::IVA])
            ),
            quantity : 10,
            taxes    : Taxes::from([
                Tax\Inclusive::proporcional(21, TaxCode::IVA),
                Tax\Inclusive::proporcional(18, TaxCode::IGV),
            ]),
            charges  : Adjusters::from([
                Charge::proporcional(5)->named(ChargeCode::POS),
                Charge::fixed(10)->named(ChargeCode::Delivery),
            ]),
            discounts: Adjusters::from([
                Discount::proporcional(2)->named(DiscountCode::NewUsers),
                Discount::fixed(5)->named(DiscountCode::Promotional),
            ])
        );
    }
}
