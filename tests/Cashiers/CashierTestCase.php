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

use function call_user_func;
use function explode;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\reduce;
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
        $this->assertTotal('unitPrice', $summary->unitPrice());
        $this->assertTotal('subtotal', $summary->subtotal());
        $this->assertTotal('taxes->total', $summary->totalTaxes());
        $this->assertTotal('charges->total', $summary->totalCharges());
        $this->assertTotal('discounts->total', $summary->totalDiscounts());
        $this->assertTotal('total', $summary->total());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier = $this->cashier();
    }

    private function assertTotal(string $label, Money $expected): void
    {
        $total = reduce(
            static fn(object $context, string $method) => call_user_func([$context, $method]),
            explode('->', $label),
            $this->cashier
        );

        $this->assertEquals(
            $expected,
            $total,
            sprintf(
                'Failed asserting that the result <%s> of Cart::%s() is equal to <%s>.',
                $total->getAmount(),
                $label,
                $expected->getAmount()
            )
        );
    }

    private function assertAdjustmentEquals(Adjustments $expected): callable
    {
        return static fn(Adjustment $adjustment) => self::assertEquals($expected->locate($adjustment->code()), $adjustment);
    }

    private function assertQuantity(int $quantity): void
    {
        $this->assertEquals($this->cashier->quantity(), $quantity);
    }
}
