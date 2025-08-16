<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'utilisateur_id');
    }

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function estResolveur(): bool
    {
        return $this->role === 'resolveur';
    }

    public function estUtilisateur(): bool
    {
        return $this->role === 'utilisateur';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}
