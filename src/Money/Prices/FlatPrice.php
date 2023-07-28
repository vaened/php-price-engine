<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money\Prices;

use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Money;

final class FlatPrice implements Price
{
    private readonly Currency $currency;

    private readonly Context  $context;

    public function __construct(
        private readonly Money $discountable,
        private readonly Money $chargeable,
        private readonly Money $taxable,
    )
    {
        $this->currency = $taxable->getCurrency();
        $this->context  = $taxable->getContext();
    }

    public function discountable(): Money
    {
        return $this->discountable;
    }

    public function chargeable(): Money
    {
        return $this->chargeable;
    }

    public function taxable(): Money
    {
        return $this->taxable;
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
