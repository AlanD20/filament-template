<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum UserPermission: string
{
    use EnumToArray;

    case DEVELOPER = 'developer';
    case SUPER_ADMIN = 'super_admin';

    public static function getSystemManagerGroup(): array
    {
        return [
            self::DEVELOPER->value,
            self::SUPER_ADMIN->value,
        ];
    }

    /**
     * Return enum in associative array with respected key and values are translated.
     */
    public static function display(): array
    {
        return [
            self::DEVELOPER->value => __('developer'),
            self::SUPER_ADMIN->value => __('super admin'),
        ];
    }

    /**
     * Return the translated text of given permission.
     */
    public static function translate(string $permission): string
    {
        return match ($permission) {
            self::DEVELOPER->value => __('developer'),
            self::SUPER_ADMIN->value => __('super admin'),
            default => ''
        };
    }
}
