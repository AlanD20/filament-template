<?php

namespace App\Services\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasCachableProperty
{
    /**
     * A wrapper to cache a given closure result with a key.
     */
    public function cacheProperty(string $key, \Closure $action, mixed $ttl = null): mixed
    {
        $value = Cache::get($key, null);

        if ($value !== null) {
            return $value;
        }

        $value = $action->call($this);

        Cache::set($key, $value, $ttl);

        return $value;
    }
}
