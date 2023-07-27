<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests;

use Brick\Money\Context\CustomContext;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Vaened\PriceEngine\Adjusters\AdjusterScheme;
use Vaened\PriceEngine\Adjusters\Adjustment;
use Vaened\PriceEngine\Adjusters\Adjustments;

abstract class TestCase extends PhpUnitTestCase
{
    private static Money $money;

    protected static function defaultAmount(): Money
    {
        return Money::zero('USD', new CustomContext(4));
    }

    protected static function collect(array $adjustments): Adjustments
    {
        $default = self::defaultAmount();
        return new Adjustments($adjustments, $default->getCurrency(), $default->getContext());
    }

    protected static function createAdjustment(float $amount, AdjusterScheme $scheme): Adjustment
    {
        return new Adjustment(self::money($amount), $scheme->type(), $scheme->mode(), $scheme->value(), $scheme->code());
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

