<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use BackedEnum;
use Brick\Money\Money;
use UnitEnum;
use Vaened\PriceEngine\Adjusters\Adjustment;

interface TotalSummary
{
    public function quantity(): int;

    public function unitPrice(): Money;

    public function subtotal(): Money;

    public function totalTaxes(): Money;

    public function charge(BackedEnum|UnitEnum|string $chargeCode): ?Adjustment;

    public function totalCharges(): Money;

    public function discount(BackedEnum|UnitEnum|string $discountCode): ?Adjustment;

    public function totalDiscounts(): Money;

    public function total(): Money;
}