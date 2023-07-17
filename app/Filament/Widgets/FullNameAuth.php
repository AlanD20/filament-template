<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class FullNameAuth extends BaseWidget
{
    protected function getCards(): array
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $name = $user->full_name;

        return [
            Card::make('welcome', $name)
                ->label(__('welcome')),
        ];
    }
}
