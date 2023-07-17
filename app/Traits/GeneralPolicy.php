<?php

namespace App\Traits;

use App\Models\User;

trait GeneralPolicy
{
    /**
     * Perform pre-authorization checks.
     *
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isInSystemManagerGroup()) {
            return true;
        }
    }
}
