<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests;

use Brick\Money\Money;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Vaened\PriceEngine\Adjusters\AdjusterScheme;
use Vaened\PriceEngine\Adjusters\Adjustment;
use Vaened\PriceEngine\Adjusters\Adjustments;

abstract class TestCase extends PhpUnitTestCase
{
    private static Money $money;

    abstract protected static function defaultAmount(): Money;

    protected static function collect(iterable $adjustments): Adjustments
    {
        return Adjustments::from(static::defaultMoney()->getCurrency(), static::defaultMoney()->getContext(), $adjustments);
    }

    protected static function createAdjustment(float $amount, AdjusterScheme $scheme): Adjustment
    {
        return new Adjustment(self::money($amount), $scheme->type(), $scheme->value(), $scheme->code());
    }

    protected static function money(float $amount): Money
    {
        return Money::of($amount, static::defaultMoney()->getCurrency(), static::defaultMoney()->getContext());
    }

    private static function defaultMoney(): Money
    {
        return self::$money ??= static::defaultAmount();
    }
}

