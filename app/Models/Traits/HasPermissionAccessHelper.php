<?php

namespace App\Models\Traits;

use App\Enums\UserPermission;

/**
 * This trait is used on User model where it has direct access to the user model when it accessses $this property.
 * For example, $this->username must return the user's username and so on.
 */
trait HasPermissionAccessHelper
{
    public function isDeveloper(): bool
    {
        return $this->hasPermissionTo(UserPermission::DEVELOPER->value);
    }

    public function isInSystemManagerGroup(): bool
    {
        return $this->hasAnyPermission(UserPermission::getSystemManagerGroup());
    }
}
