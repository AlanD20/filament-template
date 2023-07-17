<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Scopes\SoftDeletingWithTrashedScope;

trait SoftDeletesWithTrashed
{
    use SoftDeletes;

    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingWithTrashedScope);
    }
}
