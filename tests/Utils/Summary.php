<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Utils;

use Brick\Money\Money;
use Vaened\PriceEngine\TotalSummary;

final class Summary implements TotalSummary
{
    public function __construct(
        private int            $quantity,
        private readonly Money $unitPrice,
        private Money          $subtotal,
        private Money          $totalTaxes,
        private Money          $totalCharges,
        private Money          $totalDiscounts,
        private Money          $total,
    )
    {
    }

    public static function is(
        int   $quantity,
        Money $unitPrice,
        Money $subtotal,
        Money $totalTaxes,
        Money $totalCharges,
        Money $totaDiscounts,
        Money $total,
    ): self
    {
        return new self(
            $quantity,
            $unitPrice,
            $subtotal,
            $totalTaxes,
            $totalCharges,
            $totaDiscounts,
            $total,
        );
    }

    public function changeTo(
        int   $quantity,
        Money $subtotal,
        Money $totalTaxes,
        Money $totalCharges,
        Money $totalDiscounts,
        Money $total,
    ): self
    {
        $this->quantity = $quantity;
        $this->subtotal = $subtotal;
        return $this->andTotalTaxesAre($totalTaxes)
            ->andTotalChargesAre($totalCharges)
            ->andTotalDiscountsAre($totalDiscounts)
            ->andDefinitiveTotalAre($total);
    }

    public function andTotalTaxesAre(Money $money): self
    {
        $this->totalTaxes = $money;
        return $this;
    }

    public function andTotalChargesAre(Money $money): self
    {
        $this->totalCharges = $money;
        return $this;
    }

    public function andTotalDiscountsAre(Money $money): self
    {
        $this->totalDiscounts = $money;
        return $this;
    }

    public function andDefinitiveTotalAre(Money $money): self
    {
        $this->total = $money;
        return $this;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function subtotal(): Money
    {
        return $this->subtotal;
    }

    public function totalTaxes(): Money
    {
        return $this->totalTaxes;
    }

    public function totalCharges(): Money
    {
        return $this->totalCharges;
    }

    public function totalDiscounts(): Money
    {
        return $this->totalDiscounts;
    }

    public function total(): Money
    {
        return $this->total;
    }
}
