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

    public function taxes(): Modifiers;

    public function charges(): Modifiers;

    public function discounts(): Modifiers;

    public function total(): Money;
}