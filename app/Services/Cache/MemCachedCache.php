<?php

namespace App\Services\Cache;

use App\Contracts\CacheStrategyInterface;
use Illuminate\Support\Facades\Cache;

class MemCachedCache implements CacheStrategyInterface
{
    /**
     * @inheritDoc
     */
    public function put(string $key, $value, $ttl): void
    {
        Cache::store('memcached')->put($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): mixed
    {
        return Cache::store('memcached')->get($key);
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): void
    {
        Cache::store('memcached')->forget($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        Cache::store('memcached')->clear();
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return Cache::store('memcached')->has($key);
    }

    /**
     * @inheritDoc
     */
    public function remember(string $key, $ttl, callable $callback): mixed
    {
        return Cache::store('memcached')->remember($key, $ttl, $callback);
    }
}
