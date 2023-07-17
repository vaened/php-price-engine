<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjusters\AdjusterType;

class Calculator
{
    public function byInclusivePercentage(Money $money, int $percentage): Money
    {
        return $money->dividedBy(1 + ($percentage / 100), Helper::defaultRoundingMode());
    }

    public function byInclusiveAmount(Money $money, float $amount): Money
    {
        return $money->minus(
            Money::of($amount, $money->getCurrency(), $money->getContext())
        );
    }

    public function byInclusive(Money $money, AdjusterType $type, float|int $value): Money
    {
        return match ($type) {
            AdjusterType::Percentage => $this->byInclusivePercentage($money, (int)$value),
            AdjusterType::Uniform => $this->byInclusiveAmount($money, (float)$value)
        };
    }

    public function byExclusivePercentage(Money $money, int $percentage): Money
    {
        return $money->multipliedBy($percentage / 100, Helper::defaultRoundingMode());
    }

    public function byExclusiveAmount(Money $money, float $amount): Money
    {
        return Money::of($amount, $money->getCurrency(), $money->getContext());
    }

    public function byExclusive(Money $money, AdjusterType $type, float|int $value): Money
    {
        return match ($type) {
            AdjusterType::Percentage => $this->byExclusivePercentage($money, (int)$value),
            AdjusterType::Uniform => $this->byExclusiveAmount($money, (float)$value)
        };
    }
}
