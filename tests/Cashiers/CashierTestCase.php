<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustment;
use Vaened\PriceEngine\Adjustments;
use Vaened\PriceEngine\Cashier;
use Vaened\PriceEngine\Tests\TestCase;
use Vaened\PriceEngine\Tests\Utils\Summary;

use function call_user_func;
use function dd;
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
            $this->assertAdjustmentEquals($this->cashier->discounts()),
            self::collect($expected),
        );
    }

    public function assertCharges(Adjustment ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals($this->cashier->charges()),
            self::collect($expected)
        );
    }

    public function assertTaxes(Adjustment ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals($this->cashier->taxes()),
            self::collect($expected),
        );
    }

    protected function printTotals(): never
    {
        dd(
            reduce(function (array $acc, Money $total, string $label) {
                $acc[$label] = $total->getAmount();
                return $acc;
            },
                [
                    'unitPrice'      => $this->cashier->unitPrice(),
                    'subtotal'       => $this->cashier->subtotal(),
                    'totalTaxes'     => $this->cashier->taxes()->total(),
                    'totalCharges'   => $this->cashier->charges()->total(),
                    'totalDiscounts' => $this->cashier->discounts()->total(),
                    'total'          => $this->cashier->total(),
                ],
                []),

        );
    }

    protected function assertTotals(Summary $summary): void
    {
        $this->assertQuantity($summary->quantity());
        $this->assertTotal('unitPrice->gross', $summary->unitPrice());
        $this->assertTotal('subtotal->gross', $summary->subtotal());
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

    private function assertAdjustmentEquals(Adjustments $adjustments): callable
    {
        return static function (Adjustment $expected) use ($adjustments) {
            $adjustment = $adjustments->locate($expected->code());

            self::assertNotNull(
                $adjustment,
                sprintf(
                    'Failed asserting that adjuster <%s> is in the cart.',
                    $expected->code()
                )
            );

            self::assertEquals($adjustment, $expected);
        };
    }

    private function assertQuantity(int $quantity): void
    {
        $this->assertEquals($this->cashier->quantity(), $quantity);
    }
}
