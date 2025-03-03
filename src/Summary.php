<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;

interface Summary
{
    public function quantity(): int;

    public function unitPrice(): Price;

    public function subtotal(): Price;

    public function taxes(): Adjustments;

    public function charges(): Adjustments;

    public function discounts(): Adjustments;

    public function total(): Money;
}