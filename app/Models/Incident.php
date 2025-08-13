<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre','description','categorie','type','statut',
        'utilisateur_id','attribue_a','public_id',
        'priority','escalation_level','sla_due_at','resolved_at','closed_at',
    ];

    public const STATUT_NOUVEAU = 'nouveau';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_RESOLU  = 'resolu';
    public const STATUT_FERME   = 'ferme';

    public function utilisateur()   { return $this->belongsTo(User::class, 'utilisateur_id'); }
    public function gestionnaire()  { return $this->belongsTo(User::class, 'attribue_a'); }
    public function assignedUser()  { return $this->belongsTo(User::class, 'attribue_a'); }

    public function commentaires()
    {
        return $this->hasMany(IncidentComment::class)->with('auteur')->latest();
    }

    public function logs()  { return $this->hasMany(IncidentLog::class)->latest(); }
    public function lastLog(){ return $this->hasOne(IncidentLog::class)->latestOfMany(); }

    public function getIsReopenedAttribute(): bool
    {
        return ($this->lastLog?->action === 'close_rejected');
    }

    public static function normalizeStatus(string $status): string
    {
        $s = mb_strtolower($status);
        $s = str_replace(['é','É'], 'e', $s);
        return match ($s) {
            'nouveau'  => self::STATUT_NOUVEAU,
            'en_cours' => self::STATUT_EN_COURS,
            'resolu'   => self::STATUT_RESOLU,
            'ferme'    => self::STATUT_FERME,
            default    => $s,
        };
    }

    public static function labelForStatus(?string $status): string
    {
        $s = self::normalizeStatus((string) $status);
        return match ($s) {
            self::STATUT_NOUVEAU => 'Nouveau',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_RESOLU   => 'Résolu',
            self::STATUT_FERME    => 'Fermé',
            default               => ucfirst((string) $status),
        };
    }

    protected static function booted()
    {
        static::created(function ($incident) {
            if (empty($incident->public_id)) {
                $incident->public_id = self::generatePublicId();
                $hours = (int) config('itil.sla_hours.' . ($incident->priority ?? 'medium'), 48);
                $incident->sla_due_at = now()->addHours($hours);
                $incident->saveQuietly();
            }
            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => Auth::id(),
                'action'      => 'created',
                'to_status'   => $incident->statut,
                'details'     => 'Incident créé',
            ]);
        });

        static::updated(function ($incident) {
            if ($incident->isDirty('statut')) {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'user_id'     => Auth::id(),
                    'action'      => 'status_changed',
                    'from_status' => $incident->getOriginal('statut'),
                    'to_status'   => $incident->statut,
                    'details'     => 'Changement de statut (observer)',
                ]);
            }
            if ($incident->isDirty('attribue_a') && $incident->attribue_a) {
                IncidentLog::create([
                    'incident_id' => $incident->id,
                    'user_id'     => Auth::id(),
                    'action'      => 'assignment_changed',
                    'details'     => 'Assigné à user_id ' . $incident->attribue_a,
                ]);
            }
        });
    }

    public static function generatePublicId(): string
    {
        $prefix = 'INC-'.date('Ymd').'-';
        $last = self::whereDate('created_at', now()->toDateString())
            ->where('public_id','like',$prefix.'%')
            ->orderBy('public_id','desc')
            ->value('public_id');

        $seq = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $m)) {
            $seq = ((int)$m[1]) + 1;
        }
        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function setStatusWithLog(string $to, ?string $details = null): void
    {
        $from = $this->statut;
        $norm = self::normalizeStatus($to);

        $this->statut = $norm;
        if ($norm === self::STATUT_RESOLU) $this->resolved_at = now();
        if ($norm === self::STATUT_FERME)  $this->closed_at   = now();
        $this->save();

        IncidentLog::create([
            'incident_id' => $this->id,
            'user_id'     => Auth::id(),
            'action'      => 'status_changed',
            'from_status' => $from,
            'to_status'   => $norm,
            'details'     => $details,
        ]);
    }

    public function escalate(?User $to = null, string $reason = null): void
    {
        $this->escalation_level = (int) $this->escalation_level + 1;
        if ($to) $this->attribue_a = $to->id;
        $this->save();

        IncidentLog::create([
            'incident_id' => $this->id,
            'user_id'     => Auth::id(),
            'action'      => 'escalated',
            'details'     => $reason ?: 'Escalation',
            'from_status' => $this->statut,
            'to_status'   => $this->statut,
        ]);
    }

    public function scopeFilter($q, Request $request, $user)
    {
        if (method_exists($user, 'estAdmin') && $user->estAdmin()) {
            // ver todo
        } elseif (method_exists($user, 'estResolveur') && $user->estResolveur()) {
            if ($request->query('assigned') === 'me') {
                $q->where('attribue_a', $user->id);
            }
        } else {
            $q->where('utilisateur_id', $user->id);
        }

        $from = $request->input('from', $request->input('date_debut'));
        $to   = $request->input('to',   $request->input('date_fin'));
        if ($from) $q->whereDate('created_at', '>=', $from);
        if ($to)   $q->whereDate('created_at', '<=', $to);

        if ($request->filled('statut')) {
            $raw = $request->query('statut');
            $norm = self::normalizeStatus($raw);
            $q->whereIn('statut', [$raw, $norm]);
        }

        $prioReq = strtolower($request->input('priority', $request->input('priorite', '')));
        if ($prioReq !== '') {
            $q->where(function ($qq) use ($prioReq) {
                $qq->whereRaw('LOWER(priority) = ?', [$prioReq]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw("LOWER(REPLACE(REPLACE(priorite,'é','e'),'É','e')) = ?", [$prioReq]);
                }
            });
        }

        $qText = $request->input('q', $request->input('titre'));
        if ($qText) {
            $q->where(function ($qq) use ($qText) {
                $qq->where('public_id', 'like', "%{$qText}%")
                   ->orWhere('titre',     'like', "%{$qText}%")
                   ->orWhere('description','like', "%{$qText}%");
            });
        }

        return $q;
    }

    public function scopeSorted($q, Request $request)
    {
        $sort = $request->query('sort', 'date'); // 'prio' | 'date'
        $dir  = strtolower($request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'prio') {
            return $q->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 4
                    WHEN 'high'     THEN 3
                    WHEN 'medium'   THEN 2
                    WHEN 'low'      THEN 1
                    ELSE 0
                END {$dir}
            ")->orderBy('created_at', 'desc');
        }

        return $q->orderBy('created_at', $dir);
    }
}
