<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Tax;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjustmentType;
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
            static fn(Money $price, TaxScheme $taxation) => $price->minus(InclusiveAdjustmentHandler::extractFrom($price, $taxation)),
            [$this->fixedTaxes(), $this->proportionalTaxes()],
            $unitPrice
        );
    }

    private function fixedTaxes(): TaxScheme
    {
        return Inclusive::fixed(
            $this->reduceTo(AdjustmentType::Uniform, 0.0),
            'FixedTaxes'
        );
    }

    private function proportionalTaxes(): TaxScheme
    {
        return Inclusive::proportional(
            $this->reduceTo(AdjustmentType::Percentage, 0),
            'ProportionalTaxes'
        );
    }

    private function reduceTo(AdjustmentType $type, int|float $default)
    {
        return $this->taxes->filter($this->only($type))->reduce($this->sum(), $default);
    }

    private function sum(): callable
    {
        return static fn(int|float $acc, TaxScheme $taxation) => $acc + $taxation->value();
    }

    private function only(AdjustmentType $type): callable
    {
        return static fn(TaxScheme $taxation) => $taxation->type() === $type;
    }

    private function inclusives(): callable
    {
        return static fn(TaxScheme $taxation) => $taxation->isInclusive();
    }
}
