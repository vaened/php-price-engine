<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjustments;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Helper;
use Vaened\Support\Types\SecureList;

use function Lambdish\Phunctional\filter;

class Adjustments extends SecureList
{
    public function __construct(iterable $items)
    {
        parent::__construct($items);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function from(iterable $items): self
    {
        return new self($items);
    }

    public function remove(BackedEnum|UnitEnum|string $code): void
    {
        $this->items = filter(
            static fn(AdjustmentScheme $adjustment) => $adjustment->code() !== Helper::processEnumerableCode($code),
            $this->items
        );
    }

    public function push(AdjustmentScheme $adjustment): void
    {
        $this->items[] = $adjustment;
    }

    public static function type(): string
    {
        return AdjustmentScheme::class;
    }
}
