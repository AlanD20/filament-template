<?php

namespace App\Services\Concerns;

use App\Models\User;

trait HasUserModel
{
    private ?User $user = null;

    /**
     * Set user model instance if required.
     */
    public function setUserModel(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user model instance.
     */
    public function getUserModel(): ?User
    {
        return $this->user;
    }
}
