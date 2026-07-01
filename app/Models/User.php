<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'phone',
        'password',
        'status',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function ledDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'leader_id');
    }

    public function ledZones(): HasMany
    {
        return $this->hasMany(Zone::class, 'leader_id');
    }

    public function leader(): HasOne
    {
        return $this->hasOne(Leader::class, 'user_id');
    }

    public function hasRole(string $roleKey): bool
    {
        return $this->roles->contains(fn (Role $role) => $role->key === $roleKey);
    }

    public function hasPermission(string $permissionKey): bool
    {
        return $this->roles->contains(function (Role $role) use ($permissionKey) {
            // System admin role has full access across all modules.
            if ($role->key === 'system_admin') {
                return true;
            }

            return $role->permissions->contains(function (Permission $permission) use ($permissionKey) {
                return $permission->key === '*' || $permission->key === $permissionKey;
            });
        });
    }

    public function primaryRole(): ?Role
    {
        return $this->roles->sortBy('id')->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
