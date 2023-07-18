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
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class ComplexCalculationsTest extends StandardCashierTestCase
{
    public function test_calculations_are_accurate_in_stressful_situations(): void
    {
        $summary = $this->initialSummary();
        $this->assertTotals($summary);

        $this->cashier->update(3);

        $this->assertTotals(
            $summary->changeTo(
                quantity      : 3,
                subtotal      : self::money(85.1694),
                totalTaxes    : self::money(17.8305),
                totalCharges  : self::money(0),
                totalDiscounts: self::money(1.7034),
                total         : self::money(101.2965),
            )
        );

        $this->cashier->add(Charge::proporcional(4)->named(ChargeCode::Delivery));

        $this->assertTotals(
            $summary
                ->andTotalChargesAre(self::money(3.4068))
                ->andDefinitiveTotalAre(self::money(104.7033))
        );

        $this->cashier->apply(Discount::proporcional(3)->named(DiscountCode::Promotional));
        $this->assertTotals(
            $summary
                ->andTotalDiscountsAre(self::money(4.2585))
                ->andDefinitiveTotalAre(self::money(102.1482))
        );

        $this->cashier->cancelDiscount(DiscountCode::NewUsers);
        $this->assertTotals(
            $summary
                ->andTotalDiscountsAre(self::money(2.5551))
                ->andDefinitiveTotalAre(self::money(103.8516))
        );

        $this->cashier->revertCharge(ChargeCode::Delivery);

        $this->assertTotals(
            $summary
                ->andTotalChargesAre(self::money(0))
                ->andDefinitiveTotalAre(self::money(100.4448))
        );

        $this->cashier->update(9);
        $this->assertTotals(
            $summary->changeTo(
                quantity      : 9,
                subtotal      : self::money(255.5082),
                totalTaxes    : self::money(48.4915),
                totalCharges  : self::money(0),
                totalDiscounts: self::money(7.6652),
                total         : self::money(296.3345),
            )
        );
    }

    protected function cashier(): Cashier
    {
        return new StandardCashier(
            Amount::taxable(
                self::money(33.5),
                TaxCodes::only([TaxCode::IGV, TaxCode::ISC])
            ),
            quantity : 7,
            taxes    : Taxes::from([
                Tax\Inclusive::proporcional(21, TaxCode::IVA),
                Tax\Inclusive::proporcional(18, TaxCode::IGV),
                Tax\Exclusive::fixed(2.5, TaxCode::ISC)
            ]),
            discounts: Adjusters::from([
                Discount::proporcional(2)->named(DiscountCode::NewUsers),
            ])
        );
    }

    private function initialSummary(): Summary
    {
        return Summary::is(
            quantity     : 7,
            unitPrice    : self::money(28.3898),
            subtotal     : self::money(198.7286),
            totalTaxes   : self::money(38.2711),
            totalCharges : self::money(0),
            totaDiscounts: self::money(3.9746),
            total        : self::money(233.0251),
        );
    }
}
