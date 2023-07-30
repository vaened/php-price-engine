<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Handlers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjusterMode;
use Vaened\PriceEngine\Adjustments\AdjusterScheme;
use Vaened\PriceEngine\Adjustments\AdjusterType;
use Vaened\PriceEngine\PriceEngineConfig;
use Vaened\PriceEngine\Helper;

/**
 * The ExclusiveAdjustmentHandler class applies exclusive monetary adjustments.
 *
 * This class is responsible for calculating and applying exclusive adjustments to the
 * unit price depending on the quantity if applicable.
 * Exclusive adjustments are specific or proportional amounts that are applied to the
 * unit price, resulting in a total adjusted amount.
 *
 * @example
 * Given a $unitPrice of $50, a $quantity of 3, and a unique adjustment of 10% for each unit:
 * $scheme = 10% per unit
 * $AdjustedTotal = ExclusiveAdjustmentController::apply($unitPrice, $quantity, $scheme);
 * Output: $15 (10% monetary adjustment applied to each unit)
 *
 */
final class ExclusiveAdjustmentHandler
{
    public static function apply(Money $unitPrice, int $quantity, AdjusterScheme $scheme): Money
    {
        if ($scheme->type() === AdjusterType::Uniform) {
            $money = Money::of($scheme->value(), $unitPrice->getCurrency(), $unitPrice->getContext());
            return $scheme->mode() === AdjusterMode::ForTotal ? $money : $money->multipliedBy($quantity);
        }

        return $scheme->mode() === AdjusterMode::ForTotal
            ? self::extractProportionally(
                $unitPrice->multipliedBy($quantity),
                $scheme
            )
            : self::extractProportionally($unitPrice, $scheme)
                  ->multipliedBy($quantity);
    }

    private static function extractProportionally(Money $money, AdjusterScheme $scheme): Money
    {
        return $money->multipliedBy(
            Helper::percentageize($scheme->value()),
            PriceEngineConfig::defaultRoundingMode()
        );
    }
}
