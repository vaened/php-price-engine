<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Cashiers;

use Brick\Money\Money;
use Vaened\PriceEngine\Modifier;
use Vaened\PriceEngine\Modifiers;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->cashier = $this->cashier();
    }

    public function assertDiscounts(Modifier ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals($this->cashier->discounts()),
            self::collect($expected),
        );
    }

    public function assertCharges(Modifier ...$expected): void
    {
        each(
            $this->assertAdjustmentEquals($this->cashier->charges()),
            self::collect($expected)
        );
    }

    public function assertTaxes(Modifier ...$expected): void
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

    private function assertAdjustmentEquals(Modifiers $adjustments): callable
    {
        return static function (Modifier $expected) use ($adjustments) {
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


    protected function show(): void
    {
        $cashier = $this->cashier;

        echo "quantity: {$cashier->quantity()}" . PHP_EOL;
        echo "unitPrice GROSS: {$cashier->unitPrice()->gross()}" . PHP_EOL;
        echo "unitPrice NET: {$cashier->unitPrice()->net()}" . PHP_EOL;
        echo "total Discounts: {$cashier->discounts()->total()}" . PHP_EOL;
        echo "total Charges: {$cashier->charges()->total()}" . PHP_EOL;
        echo "taxes: {$cashier->taxes()->total()}" . PHP_EOL;
        echo "subtotal GROSS: {$cashier->subtotal()->gross()}" . PHP_EOL;
        echo "subtotal NET: {$cashier->subtotal()->net()}" . PHP_EOL;
        echo "total: {$cashier->total()}" . PHP_EOL;
    }
}
