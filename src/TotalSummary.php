<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjusters\Adjustments;

interface TotalSummary
{
    public function quantity(): int;

    public function unitPrice(): Money;

    public function subtotal(): Money;

    public function taxes(): Adjustments;

    public function charges(): Adjustments;

    public function discounts(): Adjustments;

    public function total(): Money;
}