<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'details',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['human_details'];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHumanDetailsAttribute(): string
    {
        $a = $this->action ?? '';
        $from = $this->from_status ? \App\Models\Incident::labelForStatus($this->from_status) : null;
        $to   = $this->to_status   ? \App\Models\Incident::labelForStatus($this->to_status)   : null;

        if ($a === 'status_changed' && $from && $to) {
            return 'Statut: '.$from.' → '.$to;
        }

        if ($a === 'priority_changed') {
            return $this->details ?: 'Priorité mise à jour';
        }

        if ($a === 'escalated') {
            return $this->details ?: 'Réassignation';
        }

        if ($a === 'closed_confirmed') {
            return 'Fermeture confirmée';
        }

        if ($a === 'close_rejected') {
            return 'Fermeture rejetée: '.(string) $this->details;
        }

        if ($a === 'comment') {
            return (string) $this->details;
        }

        if ($a === 'created') {
            return $this->details ?: 'Création de l’incident';
        }

        if ($a === 'updated') {
            $changes = $this->meta['changes'] ?? [];
            if (is_array($changes) && count($changes)) {
                $parts = [];
                foreach ($changes as $field => $pair) {
                    $old = $pair['old'] ?? '';
                    $new = $pair['new'] ?? '';
                    $parts[] = $field.": '".$old."' → '".$new."'";
                }
                return 'Mise à jour: '.implode(', ', $parts);
            }
            return 'Mise à jour';
        }

        return $this->details ?: ucfirst($a);
    }
}
