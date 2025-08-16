<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Incident extends Model
{
    use HasFactory;

    protected $table = 'incidents';

    protected $fillable = [
        'public_id',
        'titre',
        'description',
        'categorie',
        'type',
        'statut',
        'priority',
        'escalation_level',
        'sla_due_at',
        'resolved_at',
        'closed_at',
        'utilisateur_id', 
        'attribue_a',     
    ];

    protected $casts = [
        'sla_due_at'   => 'datetime',
        'resolved_at'  => 'datetime',
        'closed_at'    => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public const STATUT_NOUVEAU  = 'nouveau';
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_RESOLU   = 'resolu';
    public const STATUT_FERME    = 'ferme';
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function user()
    {
        return $this->utilisateur();
    }

    public function resolveur()
    {
        return $this->belongsTo(User::class, 'attribue_a');
    }

    public function gestionnaire()   { return $this->belongsTo(User::class, 'attribue_a'); }
    public function assignedUser()   { return $this->belongsTo(User::class, 'attribue_a'); }

    public function commentaires()   { return $this->hasMany(IncidentComment::class)->with('auteur')->latest(); }
    public function logs()           { return $this->hasMany(IncidentLog::class)->latest(); }
    public function lastLog()        { return $this->hasOne(IncidentLog::class)->latestOfMany(); }

    public function scopeFilter($query, $filters = [])
    {
        if ($filters instanceof Request) {
            $filters = $filters->all();
        }

        $search = $filters['search'] ?? $filters['q'] ?? null;
        if ($search) {
            $s = '%' . mb_strtolower($search) . '%';
            $query->where(function ($q) use ($s) {
                $q->whereRaw('LOWER(public_id) LIKE ?', [$s])
                  ->orWhereRaw('LOWER(titre) LIKE ?', [$s])
                  ->orWhereRaw('LOWER(description) LIKE ?', [$s]);
            });
        }

        $status = $filters['status'] ?? $filters['statut'] ?? null;
        if ($status) {
            $query->where('statut', self::normalizeStatus($status));
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        $from = $filters['from'] ?? $filters['du'] ?? null;
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        $to = $filters['to'] ?? $filters['au'] ?? null;
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        if (!empty($filters['mine'])) {
            $query->where('utilisateur_id', auth()->id());
        }

        if (!empty($filters['assigned_to_me'])) {
            $query->where('attribue_a', auth()->id());
        }

        return $query;
    }

    public function scopeSearch($q, ?string $term)
    {
        if (!$term) return $q;
        $t = '%'.mb_strtolower($term).'%';

        return $q->where(function ($qq) use ($t) {
            $qq->whereRaw('LOWER(public_id) LIKE ?', [$t])
               ->orWhereRaw('LOWER(titre) LIKE ?', [$t])
               ->orWhereRaw('LOWER(description) LIKE ?', [$t]);
        });
    }

    public function scopeOrderByPriority($q, string $dir = 'desc')
    {
        $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';

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

    public function scopeSorted($query, $sort = null, $dir = null)
{
    if ($sort instanceof \Illuminate\Http\Request) {
        $dir  = $sort->get('dir');
        $sort = $sort->get('sort');
    } elseif (is_array($sort)) {
        $dir  = $sort['dir']  ?? null;
        $sort = $sort['sort'] ?? null;
    }

    $sort = $sort ?: request('sort', 'created_at');
    $dir  = strtolower($dir ?: request('dir', 'desc'));
    $dir  = $dir === 'asc' ? 'asc' : 'desc';

    switch ($sort) {
        case 'priority':
            return $query->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 4
                    WHEN 'high'     THEN 3
                    WHEN 'medium'   THEN 2
                    WHEN 'low'      THEN 1
                    ELSE 0
                END {$dir}
            ")->orderBy('created_at', 'desc');
        case 'status':
        case 'statut':
            return $query->orderBy('statut', $dir)->orderBy('created_at', 'desc');
        case 'updated_at':
            return $query->orderBy('updated_at', $dir);
        default:
            return $query->orderBy('created_at', $dir);
    }
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
            self::STATUT_NOUVEAU  => 'Nouveau',
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_RESOLU   => 'Résolu',
            self::STATUT_FERME    => 'Fermé',
            default               => ucfirst($s),
        };
    }

    public function getIsReopenedAttribute(): bool
    {
        return ($this->lastLog?->action === 'close_rejected');
    }

    public static function generatePublicId(): string
    {
        $prefix = 'INC-'.date('Ymd').'-';

        $last = self::whereDate('created_at', now()->toDateString())
            ->where('public_id', 'like', $prefix.'%')
            ->orderBy('public_id', 'desc')
            ->value('public_id');

        $seq = 1;
        if ($last && preg_match('/-(\d{4})$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function setStatusWithLog(string $to, ?string $details = null): void
    {
        $from = $this->statut;
        $norm = self::normalizeStatus($to);

        $this->statut = $norm;

        if ($norm === self::STATUT_RESOLU) { $this->resolved_at = now(); }
        if ($norm === self::STATUT_FERME)  { $this->closed_at   = now(); }

        $this->save();

        IncidentLog::create([
            'incident_id' => $this->id,
            'user_id'     => Auth::id(),
            'action'      => 'status_changed',
            'from_status' => $from,
            'to_status'   => $norm,
            'details'     => $details,
        ]);

        if ($this->utilisateur) {
            $this->utilisateur->notify(new \App\Notifications\IncidentStatutUpdated($this));
        }
    }
}
