<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // return $this->hasRole('admin') || $this->hasPermissionTo('view_admin');
        return $this->hasRole('super_admin') || $this->hasRole('admin');
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function getHighestRoleLevel(): int
    {
        return $this->roles
            ->map(fn($role) => $role->getLevel())
            ->max() ?? 0;
    }

    public function hasHigherLevelThan(User $user): bool
    {
        return $this->getHighestRoleLevel() >= $user->getHighestRoleLevel();
    }

    public function getAssignableRoles()
    {
        $currentUserLevel = $this->getHighestRoleLevel();

        // Get all roles and filter based on level
        return Role::all()
            ->filter(function ($role) use ($currentUserLevel) {
                return $role->getLevel() <= $currentUserLevel;
            })
            ->pluck('name');
    }

}
