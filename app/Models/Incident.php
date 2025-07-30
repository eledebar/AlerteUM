<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'statut',
        'utilisateur_id',
        'attribué_à',
    ];

    // Utilisateur qui a créé l’incident
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    // Utilisateur à qui est attribué l’incident
    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'attribué_à');
    }
}
