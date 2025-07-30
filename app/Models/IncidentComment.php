<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncidentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'commentaire',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
