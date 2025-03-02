<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Regular;

use Vaened\PriceEngine\Adjustments\{AdjusterMode, Adjusters, Charge, Discount};
use Vaened\PriceEngine\Adjustments\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Cashiers\RegularCashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Tests\Utils\{ChargeCode, DiscountCode, Summary, TaxCode};

final class ComplexCalculationsTest extends RegularCashierTestCase
{
    public function test_calculations_are_accurate_in_stressful_situations(): void
    {
        $summary = $this->initialSummary();
        $this->assertTotals($summary);

        $this->cashier->update(6);
        $this->assertTotals(
            $summary->changeTo(
                quantity      : 6,
                subtotal      : self::money(170.3388),
                totalTaxes    : self::money(48.6612),
                totalCharges  : self::money(10.9500),
                totalDiscounts: self::money(32.2800),
                total         : self::money(197.6700),
            )
        );

        $this->cashier->add(Charge::fixed(5, AdjusterMode::PerUnit));
        $this->assertTotals(
            $summary->andTotalChargesAre(self::money(40.9500))
                    ->andDefinitiveTotalAre(self::money(227.6700))
        );

        $this->cashier->revertCharge(ChargeCode::Delivery);
        $this->assertTotals(
            $summary->andTotalChargesAre(self::money(30.0))
                    ->andDefinitiveTotalAre(self::money(216.7200))
        );

        $this->cashier->apply(Discount::proportional(6));
        $this->assertTotals(
            $summary->andTotalDiscountsAre(self::money(45.42))
                    ->andDefinitiveTotalAre(self::money(203.58))
        );

        $this->cashier->update(1);
        $this->assertTotals(
            $summary->changeTo(
                quantity      : 1,
                subtotal      : self::money(28.3898),
                totalTaxes    : self::money(8.1102),
                totalCharges  : self::money(5.0),
                totalDiscounts: self::money(7.5700),
                total         : self::money(33.9300),
            )
        );
    }

    protected function cashier(): Cashier
    {
        return new RegularCashier(
            Amount::taxable(
                self::money(33.5),
                TaxCodes::only([TaxCode::IGV, TaxCode::ISC])
            ),
            quantity : 4,
            taxes    : Taxes::from([
                Tax\Inclusive::proportional(21, TaxCode::IVA),
                Tax\Inclusive::proportional(18, TaxCode::IGV),
                Tax\Exclusive::fixed(3, TaxCode::ISC)
            ]),
            charges  : Adjusters::from([
                Charge::proportional(5)->named(ChargeCode::Delivery),
            ]),
            discounts: Adjusters::from([
                Discount::proportional(12)->named(DiscountCode::NewUsers),
                Discount::fixed(1, AdjusterMode::PerUnit)->named(DiscountCode::Promotional),
            ])
        );
    }

    private function initialSummary(): Summary
    {
        return Summary::is(
            quantity     : 4,
            unitPrice    : self::money(28.3898),
            subtotal     : self::money(113.5592),
            totalTaxes   : self::money(32.4408),
            totalCharges : self::money(7.3000),
            totaDiscounts: self::money(21.5200),
            total        : self::money(131.7800),
        );
    }
}
