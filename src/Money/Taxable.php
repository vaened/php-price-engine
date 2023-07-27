<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use Vaened\PriceEngine\Adjustments\Tax\TaxCodes;
use Vaened\PriceEngine\Adjustments\Tax\Taxes;

interface Taxable
{
    public function applicableCodes(): TaxCodes;

    public function taxes(): Taxes;
}