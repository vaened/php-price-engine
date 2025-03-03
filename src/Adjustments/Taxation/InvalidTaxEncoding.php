<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments\Taxation;

use Throwable;
use Vaened\PriceEngine\PriceException;

final class InvalidTaxEncoding extends PriceException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('The set coding is incorrect for a tax', $code, $previous);
    }
}
