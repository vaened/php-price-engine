<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Tests\Utils;

enum TaxCode
{
    case Imaginary;
    case IGV;
    case IVA;
    case ISC;
}
