<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;

interface TotalSummary
{
    public function quantity(): int;

    public function unitPrice(): Money;

    public function subtotal(): Money;

    public function totalTaxes(): Money;

    public function totalCharges(): Money;

    public function totaDiscounts(): Money;

    public function total(): Money;
}