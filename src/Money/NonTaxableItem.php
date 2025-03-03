<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money;

use Throwable;
use Vaened\PriceEngine\PriceException;

final class NonTaxableItem extends PriceException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The evaluated element cannot be subject to taxes', $code, $previous);
    }
}
