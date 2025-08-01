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
        'categorie',
        'type',
        'statut',
        'utilisateur_id',
        'attribue_a',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'attribue_a');
    }

    public function commentaires()
    {
        return $this->hasMany(IncidentComment::class);
    }



}
