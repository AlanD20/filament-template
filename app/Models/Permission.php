<?php

namespace App\Models;

use App\Enums\UserPermission;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission as PermissionModels;

class Permission extends PermissionModels
{
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('permission-order', function (Builder $builder) {
            $builder->orderBy('id', 'desc');
        });
    }

    public function scopeExcludeDeveloper(Builder $query): Builder
    {
        return $query->whereNot('name', UserPermission::DEVELOPER->value);
    }

    public function scopeOnlyLowerPermissions(Builder $query): Builder
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $excludedPermissions = collect();

        if (! $user->isDeveloper()) {
            $excludedPermissions = $excludedPermissions->merge(UserPermission::DEVELOPER->value);
        }

        if ($excludedPermissions->isNotEmpty()) {
            $query = $query->whereNotIn('name', $excludedPermissions->toArray());
        }

        return $query;
    }
}
