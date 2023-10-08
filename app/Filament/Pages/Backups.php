<?php

namespace App\Filament\Pages;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    protected static ?string $navigationIcon = 'heroicon-s-folder-arrow-down';

    protected static ?int $navigationSort = 2;

    public function mount(): void
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        abort_unless($user->isInSystemManagerGroup(), 403);
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->isInSystemManagerGroup();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.group.settings');
    }
}
