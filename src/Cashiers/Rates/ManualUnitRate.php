<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Cashiers\Rates;

use Brick\Money\Money;
use Vaened\PriceEngine\UnitRate;

final class ManualUnitRate implements UnitRate
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
