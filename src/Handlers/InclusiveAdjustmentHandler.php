<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Handlers;

use Brick\Money\Money;
use Vaened\PriceEngine\Adjustments\AdjustmentScheme;
use Vaened\PriceEngine\Adjustments\AdjustmentType;
use Vaened\PriceEngine\PriceEngineConfig;
use Vaened\PriceEngine\Helper;

/**
 * The InclusiveAdjustmentHandler class applies inclusive monetary adjustments.
 *
 * This class is responsible for extracting a specific or proportional amount that
 * is included in the total passed as a parameter, resulting in the exact amount
 * of included adjustments.
 *
 * @example
 * Given a $total amount of $100, with a 21% inclusive adjustment:
 * $scheme = 21% included
 * $adjustedTotal = InclusiveAdjustmentHandler::apply($total, $scheme);
 * Output: $17.35 (Amount withdrawn that was included in the total)
 */
final class InclusiveAdjustmentHandler
{
    public static function extractFrom(Money $total, AdjustmentScheme $scheme): Money
    {
        return match ($scheme->type()) {
            AdjustmentType::Percentage => $total->minus(
                $total->dividedBy(
                    1 + Helper::percentageize($scheme->value()),
                    PriceEngineConfig::defaultRoundingMode()
                )
            ),

            AdjustmentType::Uniform    => Money::of(
                $scheme->value(),
                $total->getCurrency(),
                $total->getContext()
            )
        };
    }
}
