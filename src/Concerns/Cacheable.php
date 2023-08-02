<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\PriceEngine\Concerns;

trait Cacheable
{
    private string $cachedValue = '';

    abstract protected function cacheIdentifier(): string;

    protected function requiresUpdate(): bool
    {
        $identifier = $this->cacheIdentifier();

        if ($this->cachedValue === $identifier) {
            return false;
        }

        $this->cachedValue = $identifier;
        return true;
    }

    protected function cleanCache(): void
    {
        $this->cachedValue = '';
    }
}