<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum DefaultSettings: string
{
    use EnumToArray;

    case MY_KEY = 'fixed_my_key';

    /**
     * Return enum in associative array with respected key and values are translated.
     */
    public static function display(): array
    {
        return [
            // self::MY_KEY->value => __('attr.fixed_my_key'),
        ];
    }

    /**
     * Return the translated text of given type.
     */
    public static function translate(string $type): string
    {
        return match ($type) {
            self::MY_KEY->value => __('attr.fixed_my_key'),
        };
    }
}
