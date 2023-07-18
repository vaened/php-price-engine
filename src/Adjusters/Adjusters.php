<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Money\Adjuster;
use Vaened\Support\Types\ArrayObject;

class Adjusters extends ArrayObject
{
    public function __construct(iterable $items)
    {
        parent::__construct($items);
    }

    public function remove(BackedEnum|UnitEnum|string $code): void
    {
        $this->items = $this->filter(static fn(Adjuster $adjuster) => $adjuster->code() !== $code);
    }

    public function create(Adjuster $adjuster): void
    {
        $this->items[] = $adjuster;
    }

    protected function type(): string
    {
        return Adjuster::class;
    }
}
