<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use Vaened\PriceEngine\Adjustments\Taxation\TaxCodes;
use Vaened\PriceEngine\Adjustments\Taxation\Taxes;

interface Taxable
{
    public function applicableCodes(): TaxCodes;

    public function taxes(): Taxes;
}