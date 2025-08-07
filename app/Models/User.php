<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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


    public function estResolveur(): bool
    {
        return $this->role === 'resolveur';
    }

    public function estUtilisateur(): bool
    {
        return $this->role === 'utilisateur';
    }
}
