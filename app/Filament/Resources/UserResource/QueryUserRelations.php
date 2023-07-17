<?php

namespace App\Filament\Resources\UserResource;

use App\Models\Permission;
use App\Enums\UserPermission;

trait QueryUserRelations
{
    protected function getUserPermissions()
    {
        return Permission::query()
            ->onlyLowerPermissions()
            ->get()
            ->pluck('name', 'id')
            ->map(fn ($value) => UserPermission::translate($value));
    }

    private function getCurrentRecord()
    {
        return $this->record ?? auth()->user();
    }
}
