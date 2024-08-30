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

class Adjusters extends SecureList
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
            static fn(AdjusterScheme $adjuster) => $adjuster->code() !== Helper::processEnumerableCode($code),
            $this->items
        );
    }

    public function push(AdjusterScheme $adjuster): void
    {
        $this->items[] = $adjuster;
    }

    public static function type(): string
    {
        return AdjusterScheme::class;
    }
}
