<?php

namespace App\Services\Cache;

use App\Contracts\CacheStrategyInterface;

class CacheContext
{
    public CacheStrategyInterface $strategy;

    public function __construct(CacheStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(CacheStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function put(string $key, $value, $ttl): void
    {
        $this->strategy->put($key, $value, $ttl);
    }

    public function get(string $key)
    {
        return $this->strategy->get($key);
    }

    public function forget(string $key): void
    {
        $this->strategy->forget($key);
    }

    public function clear(): void
    {
        $this->strategy->clear();
    }

    public function has(string $key): bool
    {
       return $this->strategy->has($key);
    }

    public function remember(string $key, $ttl, callable $callback)
    {
        return $this->strategy->remember($key, $ttl, $callback);
    }
}
