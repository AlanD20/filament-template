<?php

namespace App\Services;

use App\Models\Settings;
use App\Enums\DefaultSettings;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    use Concerns\HasMake;
    use Concerns\HasCachableProperty;

    // public const MY_CACHE_KEY = 'my_cache_key';

    // public function getMySetting(): string
    // {
    //     return $this->cacheProperty(
    //         static::MY_CACHE_KEY,
    //         fn () => Settings::query()
    //             ->where('key', DefaultSettings::MY_KEY->value)
    //             ->first()
    //             ->value,
    //         now()->addHour()
    //     );
    // }

    public function clearCachedValues(): void
    {
        // Cache::forget(static::MY_CACHE_KEY);
    }
}
