<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
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
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function canAccessPanel(Panel $panel): bool
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
