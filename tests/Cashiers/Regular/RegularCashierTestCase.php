<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\AdjustmentMode;
use Vaened\PriceEngine\Adjustments\Adjustments;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Taxation\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjustments\Taxation;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Cashiers\RegularCashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Tests\Cashiers\CashierTestCase;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

abstract class RegularCashierTestCase extends CashierTestCase
{
    protected function cashier(): Cashier
    {
        return new RegularCashier(
            Amount::taxable(
                self::money(100),
                TaxCodes::only([TaxCode::IGV, TaxCode::ISC])
            ),
            quantity : 6,
            taxes    : Taxes::from([
                Taxation\Inclusive::proportional(18, TaxCode::IGV),
                Taxation\Inclusive::fixed(2, TaxCode::ISC),
            ]),
            charges  : Adjustments::from([
                Charge::proportional(5)->named(ChargeCode::POS),
                Charge::fixed(2, AdjustmentMode::PerUnit)->named(ChargeCode::Delivery)
            ]),
            discounts: Adjustments::from([
                Discount::proportional(2)->named(DiscountCode::NewUsers),
            ])
        );
    }
}
