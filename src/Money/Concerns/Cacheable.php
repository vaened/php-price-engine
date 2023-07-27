<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Money\Concerns;

use function sprintf;

trait Cacheable
{
    private string $lasAmount = '';

    protected function needsRecalculation(): bool
    {
        $current = sprintf('[%s]X[%d]', $this->unitPrice->getAmount()->toBigDecimal()->__toString(), $this->quantity);

        if ($this->lasAmount === $current) {
            return false;
        }

        $this->lasAmount = $current;
        return true;
    }

    protected function forceRecalculation(): void
    {
        $this->lasAmount = '';
    }
}