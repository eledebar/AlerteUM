<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Vérifie si l'utilisateur est administrateur
    public function estAdmin()
    {
        return $this->role === 'admin';
    }

    // Vérifie si l'utilisateur est gestionnaire
    public function estGestionnaire()
    {
        return $this->role === 'gestionnaire';
    }

    // Vérifie si l'utilisateur est un utilisateur simple
    public function estUtilisateur()
    {
        return $this->role === 'utilisateur';
    }
}
