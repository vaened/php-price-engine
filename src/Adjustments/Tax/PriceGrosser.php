<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Tax;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjusterType;
use Vaened\PriceEngine\Handlers\InclusiveAdjustmentHandler;

use function Lambdish\Phunctional\reduce;

final class PriceGrosser
{
    private readonly Taxes $taxes;

    public function __construct(Taxes $taxes)
    {
        $this->taxes = $taxes->filter($this->inclusives());
    }

    public static function for(Taxes $taxes): self
    {
        return new self($taxes);
    }

    public function clean(Money $unitPrice): Money
    {
        return reduce(
            static fn(Money $price, Taxation $taxation) => $price->minus(InclusiveAdjustmentHandler::extractFrom($price, $taxation)),
            [$this->fixedTaxes(), $this->proportionalTaxes()],
            $unitPrice
        );
    }

    private function fixedTaxes(): Taxation
    {
        return Inclusive::fixed(
            $this->reduceTo(AdjusterType::Uniform, 0.0),
            'FixedTaxes'
        );
    }

    private function proportionalTaxes(): Taxation
    {
        return Inclusive::proporcional(
            $this->reduceTo(AdjusterType::Percentage, 0),
            'ProportionalTaxes'
        );
    }

    private function reduceTo(AdjusterType $type, int|float $default)
    {
        return $this->taxes->filter($this->only($type))->reduce($this->sum(), $default);
    }

    private function sum(): callable
    {
        return static fn(int|float $acc, Taxation $taxation) => $acc + $taxation->value();
    }

    private function only(AdjusterType $type): callable
    {
        return static fn(Taxation $taxation) => $taxation->type() === $type;
    }

    private function inclusives(): callable
    {
        return static fn(Taxation $taxation) => $taxation->isInclusive();
    }
}
