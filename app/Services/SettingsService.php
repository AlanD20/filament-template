<?php

namespace App\Services;

use App\Models\Settings;
use App\Enums\DefaultSettings;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    use Concerns\HasMake;

    /**
     * Dynamically get any value from DefaultSettings enum.
     * the names are from the case names in camel case.
     *
     * Available methods: get__, clear__, clearAll
     */
    public static function __callStatic(string $method, array $args)
    {
        $method = str($method);
        $defaultSettings = DefaultSettings::array();

        if ($method->startsWith('get')) {
            $key = $method->after('get')->snake()->upper()->toString();

            if (array_key_exists($key, $defaultSettings)) {
                return Cache::rememberForever(
                    'CACHE_' . $key,
                    fn () => Settings::query()
                        ->where('key', $defaultSettings[$key])
                        ->first()
                        ->value,
                );
            }
        } elseif ($method->startsWith('clear')) {
            $key = $method->after('clear')->snake()->upper()->toString();

            if (array_key_exists($key, $defaultSettings)) {
                return Cache::forget('CACHE_' . $key);
            }
        } elseif ($method->is('clearAll')) {
            foreach ($defaultSettings as $value) {
                Cache::forget('CACHE_' . $value);
            }
        }
    }

    /**
     * If you would like to manage it manually, you may use the below implementation.
     */

    // protected string $MY_CUSTOM_CACHE_KEY = 'my_custom_cache_key';

    // public function clearCachedValues(): void
    // {
    //     $this->clearMyCustomSetting();
    // }

    // public function getMyCustomSetting(): string
    // {
    //     return Cache::rememberForever(
    //         $this->MY_CUSTOM_CACHE_KEY,
    //         fn () => Settings::query()
    //             ->where('key', DefaultSettings::MY_CUSTOM_CACHE_KEY->value)
    //             ->first()
    //             ->value
    //     );
    // }

    // public function clearMyCustomSetting(): void
    // {
    //     Cache::forget($this->MY_CUSTOM_CACHE_KEY);
    // }
}
