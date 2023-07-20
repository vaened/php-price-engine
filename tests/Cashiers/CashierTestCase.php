<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjusters\Adjustment;
use Vaened\PriceEngine\Adjusters\Adjustments;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Tests\TestCase;
use Vaened\PriceEngine\Tests\Utils\Summary;

use function Lambdish\Phunctional\each;
use function sprintf;

abstract class CashierTestCase extends TestCase
{
    protected readonly Cashier $cashier;

    abstract protected function cashier(): Cashier;

    public function assertDiscounts(Adjustment ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals(self::collect($expected)),
            $this->cashier->discounts()
        );
    }

    public function assertCharges(Adjustment ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals(self::collect($expected)),
            $this->cashier->charges()
        );
    }

    public function assertTaxes(Adjustment ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals(self::collect($expected)),
            $this->cashier->taxes()
        );
    }

    protected function assertTotals(Summary $summary): void
    {
        $this->assertQuantity($summary->quantity());
        $this->assertUnitPrice($summary->unitPrice());
        $this->assertSubtotal($summary->subtotal());
        $this->assertTotalTaxes($summary->totalTaxes());
        $this->assertTotalCharges($summary->totalCharges());
        $this->assertTotaDiscounts($summary->totalDiscounts());
        $this->assertTotal($summary->total());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier = $this->cashier();
    }

    private function assertAdjustmentEquals(Adjustments $expected): callable
    {
        return static fn(Adjustment $adjustment) => self::assertEquals($expected->locate($adjustment->code()), $adjustment);
    }

    private function assertQuantity(int $quantity): void
    {
        $this->assertEquals($this->cashier->quantity(), $quantity);
    }

    private function assertUnitPrice(Money $unitPrice): void
    {
        $this->assertEquals($this->cashier->unitPrice(),
            $unitPrice,
            sprintf(
                'Failed asserting that the unit price of <%s> equals <%s>.',
                $unitPrice->getAmount(),
                $this->cashier->unitPrice()->getAmount()
            )
        );
    }

    private function assertSubtotal(Money $subtotal): void
    {
        $this->assertEquals($this->cashier->subtotal(),
            $subtotal,
            sprintf(
                'Failed asserting that the subtotal of <%s> equals <%s>.',
                $subtotal->getAmount(),
                $this->cashier->subtotal()->getAmount()
            ));
    }

    private function assertTotalTaxes(Money $totalTaxes): void
    {
        $total = $this->cashier->taxes()->total();

        $this->assertEquals(
            $total,
            $totalTaxes,
            sprintf(
                'Failed asserting that the total taxes of <%s> equals <%s>.',
                $totalTaxes->getAmount(),
                $total
            ));
    }

    private function assertTotalCharges(Money $totalCharges): void
    {
        $total = $this->cashier->charges()->total();

        $this->assertEquals(
            $total,
            $totalCharges,
            sprintf(
                'Failed asserting that the total charges of <%s> equals <%s>.',
                $totalCharges->getAmount(),
                $total->getAmount()
            ));
    }

    private function assertTotaDiscounts(Money $totaDiscounts): void
    {
        $total = $this->cashier->discounts()->total();

        $this->assertEquals(
            $total,
            $totaDiscounts,
            sprintf(
                'Failed asserting that the tota discounts of <%s> equals <%s>.',
                $totaDiscounts->getAmount(),
                $total->getAmount()));
    }

    private function assertTotal(Money $total): void
    {
        $this->assertEquals($this->cashier->total(),
            $total,
            sprintf(
                'Failed asserting that the definitive total of <%s> equals <%s>.',
                $total->getAmount(),
                $this->cashier->total()->getAmount()
            ));
    }
}
