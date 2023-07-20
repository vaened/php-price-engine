<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money\Prices;

use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;

final class RegularPrice implements Price
{
    private readonly Money    $grossPrice;

    private readonly Currency $currency;

    private readonly Context  $context;

    public function __construct(Money $unitPrice)
    {
        $this->currency   = $unitPrice->getCurrency();
        $this->context    = $unitPrice->getContext();
        $this->grossPrice = $unitPrice;
    }

    public function discountable(): Money
    {
        return $this->grossPrice;
    }

    public function taxable(): Money
    {
        return $this->grossPrice;
    }

    public function chargeable(): Money
    {
        return $this->grossPrice;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function context(): Context
    {
        return $this->context;
    }
}
