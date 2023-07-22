<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName, HasAvatar, HasMedia
{
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use InteractsWithMedia;
    use Traits\HasPermissionAccessHelper;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'full_name',
        'username',
        'is_active',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function canAccessFilament(): bool
    {
        return $this->is_active;
    }

    public function getFilamentName(): string
    {
        return $this->full_name;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->getFirstMedia('avatars');

        return $avatar ? $avatar->getUrl() : null;
    }

    public function registerMediaCollection(): void
    {
        $this->addMediaCollection('avatars')->singleFile();
    }
}
