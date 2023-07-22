<?php

namespace App\Filament\Pages;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups;

class Backup extends Backups
{
    public function mount(): void
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        abort_unless($user->isInSystemManagerGroup(), 403);
    }

    protected static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        return $user->isInSystemManagerGroup();
    }
}
