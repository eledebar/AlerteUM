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
    ];

    // Accesor que nos da el texto “bonito”
    protected $appends = ['human_details'];

    /* Cachecillo local para no repetir consultas al resolver nombres */
    protected static array $userNameCache = [];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devuelve el detalle legible:
     * - Sustituye “user_id N” por el nombre del usuario
     * - Si no hay details pero sí cambio de estado, construye “Statut: X → Y”
     */
    public function getHumanDetailsAttribute(): string
    {
        $text = trim((string) ($this->details ?? ''));

        // Si no hay texto y fue un cambio de estado, formateamos con labels de config/itil.php
        if ($text === '' && $this->action === 'status_changed') {
            $labels = (array) config('itil.labels.status', []);
            $from = $labels[$this->from_status] ?? $this->from_status ?? '-';
            $to   = $labels[$this->to_status]   ?? $this->to_status   ?? '-';
            $text = "Statut: {$from} → {$to}";
        }

        if ($text === '') {
            return '';
        }

        // Reemplaza cualquier “user_id 123” o “user_id:123” por el nombre
        $text = preg_replace_callback('/user_id\s*:?\s*(\d+)/i', function ($m) {
            $uid = (int) $m[1];
            if (! isset(self::$userNameCache[$uid])) {
                $user = \App\Models\User::find($uid);
                self::$userNameCache[$uid] = $user?->name ?? "user_id {$uid}";
            }
            return self::$userNameCache[$uid];
        }, $text);

        return $text;
    }
}
