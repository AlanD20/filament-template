<?php

use Filament\Tables\Columns;
use App\Enums\UserPermission;
use Filament\Notifications\Notification;

if (! function_exists('notify_no_permission')) {
    /**
     * Sends a notification to warn user about insufficient permission
     * to perform an action.
     */
    function notify_no_permission(): bool
    {
        Notification::make()
            ->danger()
            ->title(__('notify.insufficient_permission.title'))
            ->body(__('notify.insufficient_permission.body'))
            ->send();

        return false;
    }
}

if (! function_exists('get_production_storage_path')) {
    /**
     * return production storage path, otherwise $fallback
     */
    function get_production_path(string $fallback, string $appends = ''): string
    {
        return env('APP_ENV', 'local') === 'production' ? env('PRODUCTION_PATH', '/srv/filament-template') . $appends : $fallback;
    }
}

if (! function_exists('generate_file_name')) {
    /**
     * Generate a random file name and appends given extension.
     */
    function generate_file_name(string $originalExtension): string
    {
        return str(now()->format('d-m-Y'))
            ->append('_', str()->random(15), '.', $originalExtension)
            ->toString();
    }
}

if (! function_exists('format_money')) {
    /**
     * Returns formatted value as money.
     */
    function format_money(string|int $value, int $decimals = 2, ?string $suffix = null): string
    {
        $format = \number_format((float) $value, $decimals);

        return $suffix ? "{$format} {$suffix}" : $format;
    }
}

if (! function_exists('translate_column_value')) {
    /**
     * Translate table column values specifically.
     */
    function translate_column_value(Columns\Column $column, array $row): string
    {
        $value = $row[$column->getName()] ?? '';

        $isBooleanColumn = $column instanceof Columns\IconColumn || $column instanceof Columns\ToggleColumn;

        if ($isBooleanColumn) {
            $value = filled($value) ? __('true') : __('false');
        }

        return $value;
    }
}

if (! function_exists('translate_notification')) {
    /**
     * Translates notification section specifically.
     */
    function translate_notification(string $type, array $data): array
    {
        $data = collect(\data_get($data, $type, []));

        if ($data->has('permissions')) {
            $permission = $data->get('permissions');
            $data->put('permissions', UserPermission::translate($permission));
        }

        return $data->toArray();
    }
}

if (! function_exists('get_boolean_if_production')) {
    /**
     * Returns state boolean value in production.
     */
    function get_boolean_if_production(bool $state = true): bool
    {
        if (app()->environment('production')) {
            return $state;
        }

        return ! $state;
    }
}

if (! function_exists('array_key_invoke')) {
    /**
     * Invoke array key if exists, otherwise return null
     */
    function array_key_invoke(array $data, string $key): mixed
    {
        return array_key_exists($key, $data) ? $data[$key]() : null;
    }
}
