<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers\Simple;

use Vaened\PriceEngine\Adjustments\AdjustmentMode;
use Vaened\PriceEngine\Adjustments\Adjustments;
use Vaened\PriceEngine\Adjustments\Charge;
use Vaened\PriceEngine\Adjustments\Discount;
use Vaened\PriceEngine\Adjustments\Tax\{TaxCodes, Taxes};
use Vaened\PriceEngine\Adjustments\Tax;
use Vaened\PriceEngine\Cashiers\SimpleCashier;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Money\Amount;
use Vaened\PriceEngine\Tests\Utils\ChargeCode;
use Vaened\PriceEngine\Tests\Utils\DiscountCode;
use Vaened\PriceEngine\Tests\Utils\Summary;
use Vaened\PriceEngine\Tests\Utils\TaxCode;

final class ComplexCalculationsTest extends SimpleCashierTestCase
{
    public function test_calculations_are_accurate_in_stressful_situations(): void
    {
        $summary = $this->initialSummary();
        $this->assertTotals($summary);

        $this->cashier->update(3);
        $this->assertTotals(
            $summary->changeTo(
                quantity      : 3,
                subtotal      : self::money(108.0000),
                totalTaxes    : self::money(22.8306),
                totalCharges  : self::money(0),
                totalDiscounts: self::money(1.7034),
                total         : self::money(106.2966),
            )
        );

        $this->cashier->add(Charge::proportional(4)->named(ChargeCode::Delivery));
        $this->assertTotals(
            $summary
                ->andTotalChargesAre(self::money(3.4068))
                ->andDefinitiveTotalAre(self::money(109.7034))
        );

        $this->cashier->apply(Discount::fixed(6, AdjustmentMode::PerUnit)->named(DiscountCode::Promotional));
        $this->assertTotals(
            $summary
                ->andTotalDiscountsAre(self::money(19.7034))
                ->andDefinitiveTotalAre(self::money(91.7034))
        );

        $this->cashier->cancelDiscount(DiscountCode::NewUsers);
        $this->assertTotals(
            $summary
                ->andTotalDiscountsAre(self::money(18.0))
                ->andDefinitiveTotalAre(self::money(93.4068))
        );

        $this->cashier->revertCharge(ChargeCode::Delivery);
        $this->assertTotals(
            $summary
                ->andTotalChargesAre(self::money(0))
                ->andDefinitiveTotalAre(self::money(90.0000))
        );

        $this->cashier->update(9);
        $this->assertTotals(
            $summary->changeTo(
                quantity      : 9,
                subtotal      : self::money(324.0000),
                totalTaxes    : self::money(68.4918),
                totalCharges  : self::money(0),
                totalDiscounts: self::money(54.0),
                total         : self::money(270.0000),
            )
        );
    }

    protected function cashier(): Cashier
    {
        return new SimpleCashier(
            Amount::taxable(
                self::money(33.5),
                TaxCodes::only([TaxCode::IGV, TaxCode::ISC])
            ),
            quantity : 7,
            taxes    : Taxes::from([
                Tax\Inclusive::proportional(21, TaxCode::IVA),
                Tax\Inclusive::proportional(18, TaxCode::IGV),
                Tax\Exclusive::fixed(2.5, TaxCode::ISC)
            ]),
            discounts: Adjustments::from([
                Discount::proportional(2)->named(DiscountCode::NewUsers),
            ])
        );
    }

    private function initialSummary(): Summary
    {
        return Summary::is(
            quantity     : 7,
            unitPrice    : self::money(36.0000),
            subtotal     : self::money(252.0000),
            totalTaxes   : self::money(53.2714),
            totalCharges : self::money(0),
            totaDiscounts: self::money(3.9746),
            total        : self::money(248.0254),
        );
    }
}
