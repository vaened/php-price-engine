<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money\Prices;

use Brick\Money\Money;

final class FlatPrice implements Price
{
    public function __construct(
        private readonly Money $discountable,
        private readonly Money $chargeable,
        private readonly Money $taxable,
    )
    {
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
}
