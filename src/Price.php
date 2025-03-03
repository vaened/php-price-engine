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
        private readonly Money $netUnitPrice,
        private readonly Money $grossUnitPrice,
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
            $this->netUnitPrice->multipliedBy($quantity),
            $this->grossUnitPrice->multipliedBy($quantity),
        );
    }
}
