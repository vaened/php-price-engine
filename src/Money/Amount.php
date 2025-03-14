<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\Taxation\TaxCodes;
use Vaened\PriceEngine\Adjustments\Taxation\Taxes;

use function count;

final class Amount implements Priceable
{
    private ?Taxes $taxes;

    public function __construct(
        private readonly Money    $unitPrice,
        private readonly TaxCodes $applicableTaxCodes,
        Taxes                     $taxes = null,
    )
    {
        $this->ensureCanHaveTaxes($taxes);
        $this->taxes = $taxes;
    }

    public static function taxable(Money $unitPrice, TaxCodes $codes = new TaxCodes(TaxCodes::ANY)): self
    {
        return new self($unitPrice, applicableTaxCodes: $codes);
    }

    public static function taxexempt(Money $unitPrice): self
    {
        return new self($unitPrice, applicableTaxCodes: TaxCodes::none());
    }

    public function impose(Taxes|array $taxes): self
    {
        $this->ensureCanHaveTaxes($taxes);
        $this->taxes = $taxes instanceof Taxes ? $taxes : Taxes::from($taxes);
        return $this;
    }

    public function value(): Money
    {
        return $this->unitPrice;
    }

    public function taxes(): Taxes
    {
        return $this->taxes ??= Taxes::empty();
    }

    public function applicableCodes(): TaxCodes
    {
        return $this->applicableTaxCodes;
    }

    private function ensureCanHaveTaxes(Taxes|array|null $taxes): void
    {
        if (
            null !== $taxes &&
            count($taxes) > 0 &&
            $this->applicableCodes()->isNothingAllowed()
        ) {
            throw new NonTaxableItem();
        }
    }
}
