<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Adjusters;

use BackedEnum;
use UnitEnum;
use Vaened\PriceEngine\Helper;
use Vaened\Support\Types\ArrayObject;

use function Lambdish\Phunctional\filter;

class Adjusters extends ArrayObject
{
    public function __construct(array $items)
    {
        parent::__construct($items);
    }

    public static function from(array $items): self
    {
        return new self($items);
    }

    public function remove(BackedEnum|UnitEnum|string $code): void
    {
        $this->items = filter(
            static fn(MoneyAdjuster $adjuster) => $adjuster->code() !== Helper::processEnumerableCode($code),
            $this->items
        );
    }

    public function create(MoneyAdjuster $adjuster): void
    {
        $this->items[] = $adjuster;
    }

    protected function type(): string
    {
        return MoneyAdjuster::class;
    }
}
