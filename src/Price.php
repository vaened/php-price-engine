<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;

final class Price
{
    public function __construct(
        private readonly Money $grossUnitPrice,
        private readonly Money $netUnitPrice,
    )
    {
    }

    public function gross(): Money
    {
        return $this->grossUnitPrice;
    }

    public function net(): Money
    {
        return $this->netUnitPrice;
    }

    public function multipliedBy(int $quantity): self
    {
        return new self(
            $this->grossUnitPrice->multipliedBy($quantity),
            $this->netUnitPrice->multipliedBy($quantity),
        );
    }
}
