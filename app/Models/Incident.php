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
        'attribue_a',
    ];

    /**
     * Utilisateur qui a signalé l’incident
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Administrateur à qui l’incident est attribué
     */
    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'attribue_a');
    }

    /**
     * Commentaires liés à l’incident
     */
    public function commentaires()
    {
        return $this->hasMany(IncidentComment::class);
    }
}
