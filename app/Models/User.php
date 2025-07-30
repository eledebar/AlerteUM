<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Champs autorisés pour l’insertion de masse
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // utilisateur, admin, gestionnaire
    ];

    // Champs à masquer dans les réponses JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casting automatique des champs
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔐 Vérifie si l'utilisateur est un administrateur
    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // 🛠️ Vérifie si l'utilisateur est un gestionnaire
    public function estGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    // 👤 Vérifie si c’est un utilisateur simple
    public function estUtilisateur(): bool
    {
        return $this->role === 'utilisateur';
    }

    // 🛡️ Vérifie si l’utilisateur a accès à tous les incidents
    public function estAdminOuGestionnaire(): bool
    {
        return in_array($this->role, ['admin', 'gestionnaire']);
    }

    // 🔗 Relation avec les incidents créés
    public function incidentsCréés()
    {
        return $this->hasMany(Incident::class, 'utilisateur_id');
    }

    // 🔗 Relation avec les incidents assignés à l’utilisateur
    public function incidentsAttribués()
    {
        return $this->hasMany(Incident::class, 'attribué_à');
    }
}
