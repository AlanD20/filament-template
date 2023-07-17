<?php

return [
    'datetime_format' => 'd/m/Y H:i:s',
    'date_format' => 'd/m/Y',

    // 'activity_resource' => \Z3d0X\FilamentLogger\Resources\ActivityResource::class,
    'activity_resource' => \App\Filament\Resources\ActivityResource::class,

    'resources' => [
        'enabled' => true,
        'log_name' => 'resource',
        'logger' => \App\Services\Logger\ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            //App\Filament\Resources\UserResource::class,
        ],
    ],

    'access' => [
        'enabled' => true,
        'logger' => \Z3d0X\FilamentLogger\Loggers\AccessLogger::class,
        'color' => 'danger',
        'log_name' => 'access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => \App\Services\Logger\NotificationLogger::class,
        'color' => null,
        'log_name' => 'notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'model',
        'color' => 'warning',
        'logger' => \App\Services\Logger\ModelLogger::class,
        'register' => [
            App\Models\User::class,
            App\Models\Settings::class,
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ],
];
