<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\Adjustments;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Taxation\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjustments\Taxation;
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
                Taxation\Inclusive::proportional(21, TaxCode::IVA),
                Taxation\Inclusive::proportional(18, TaxCode::IGV),
            ]),
            charges  : Adjustments::from([
                Charge::proportional(5)->named(ChargeCode::POS),
                Charge::fixed(10)->named(ChargeCode::Delivery),
            ]),
            discounts: Adjustments::from([
                Discount::proportional(2)->named(DiscountCode::NewUsers),
                Discount::fixed(5)->named(DiscountCode::Promotional),
            ])
        );
    }
}
