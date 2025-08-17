<?php

namespace App\Http\Controllers\Resolveur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentLog;

class ResolveurIncidentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = (int) $request->integer('per_page', 10);
        if ($perPage < 1) $perPage = 1;
        if ($perPage > 200) $perPage = 200;

        $q = Incident::query();

        if ($request->query('assigned') === 'me') {
            $q->where('attribue_a', $user->id);
        }

        $statut = $request->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($request->query('priority', $request->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        if (in_array($request->query('reopened'), ['1','true','yes','on'], true)) {
            $q->whereHas('logs', function($l){
                $l->whereIn('action', ['close_rejected','reopened']);
            });
        }

        $de = $request->query('from');
        $a  = $request->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $request->query('q', $request->query('text', '')));
        if ($terme !== '') {
            $termeSansEspaces = preg_replace('/\s+/', '', $terme);
            $termeUpper = strtoupper($termeSansEspaces);
            $chiffres = preg_replace('/\D+/', '', $termeUpper);
            $idNum = $chiffres !== '' ? (int) ltrim($chiffres, '0') : null;

            $q->where(function ($qq) use ($terme, $idNum) {
                $qq->where('titre', 'like', '%'.$terme.'%')
                   ->orWhere('description', 'like', '%'.$terme.'%')
                   ->orWhere('public_id', 'like', '%'.$terme.'%');
                if ($idNum !== null && $idNum > 0) {
                    $qq->orWhere('id', $idNum);
                }
            });
        }

        $sort = $request->query('sort', 'created_at');
        $dir  = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'priority' || $sort === 'prio') {
            $exprPriority =
                "CASE lower(priority)
                    WHEN 'critical' THEN 4
                    WHEN 'high' THEN 3
                    WHEN 'medium' THEN 2
                    WHEN 'low' THEN 1
                    WHEN 'critique' THEN 4
                    WHEN 'haute' THEN 3
                    WHEN 'moyenne' THEN 2
                    WHEN 'basse' THEN 1
                    ELSE 0
                 END";
            $q->orderByRaw($exprPriority.' '.$dir)->orderBy('created_at', 'desc');
        } else {
            $trisAutorises = ['created_at','priority','priorite','statut','escalation_level','sla_due_at','public_id','titre','id'];
            $triDemande = $sort;
            if ($triDemande === 'priorite' && !Schema::hasColumn('incidents', 'priorite')) {
                $triDemande = 'priority';
            }
            $tri = in_array($triDemande, $trisAutorises, true) ? $triDemande : 'created_at';
            $q->orderBy($tri, $dir);
        }

        $q->with(['assignedUser','utilisateur','lastLog','logs']);

        $incidents = $q->paginate($perPage)->withQueryString();

        return view('resolveur.incidents.index', compact('incidents'));
    }

    public function datatable(Request $request)
    {
        $user = Auth::user();

        $perPage = (int) $request->integer('per_page', 10);
        if ($perPage < 1) $perPage = 1;
        if ($perPage > 200) $perPage = 200;

        $q = Incident::query();

        if ($request->query('assigned') === 'me') {
            $q->where('attribue_a', $user->id);
        }

        $statut = $request->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($request->query('priority', $request->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        if (in_array($request->query('reopened'), ['1','true','yes','on'], true)) {
            $q->whereHas('logs', function($l){
                $l->whereIn('action', ['close_rejected','reopened']);
            });
        }

        $de = $request->query('from');
        $a  = $request->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $request->query('q', $request->query('text', '')));
        if ($terme !== '') {
            $termeSansEspaces = preg_replace('/\s+/', '', $terme);
            $termeUpper = strtoupper($termeSansEspaces);
            $chiffres = preg_replace('/\D+/', '', $termeUpper);
            $idNum = $chiffres !== '' ? (int) ltrim($chiffres, '0') : null;

            $q->where(function ($qq) use ($terme, $idNum) {
                $qq->where('titre', 'like', '%'.$terme.'%')
                   ->orWhere('description', 'like', '%'.$terme.'%')
                   ->orWhere('public_id', 'like', '%'.$terme.'%');
                if ($idNum !== null && $idNum > 0) {
                    $qq->orWhere('id', $idNum);
                }
            });
        }

        $sort = $request->query('sort', 'created_at');
        $dir  = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        if ($sort === 'priority' || $sort === 'prio') {
            $exprPriority =
                "CASE lower(priority)
                    WHEN 'critical' THEN 4
                    WHEN 'high' THEN 3
                    WHEN 'medium' THEN 2
                    WHEN 'low' THEN 1
                    WHEN 'critique' THEN 4
                    WHEN 'haute' THEN 3
                    WHEN 'moyenne' THEN 2
                    WHEN 'basse' THEN 1
                    ELSE 0
                 END";
            $q->orderByRaw($exprPriority.' '.$dir)->orderBy('created_at', 'desc');
        } else {
            $trisAutorises = ['created_at','priority','priorite','statut','escalation_level','sla_due_at','public_id','titre','id'];
            $triDemande = $sort;
            if ($triDemande === 'priorite' && !Schema::hasColumn('incidents', 'priorite')) {
                $triDemande = 'priority';
            }
            $tri = in_array($triDemande, $trisAutorises, true) ? $triDemande : 'created_at';
            $q->orderBy($tri, $dir);
        }

        $q->with(['assignedUser','utilisateur','logs']);

        $paginator = $q->paginate($perPage)->withQueryString();

        $data = $paginator->getCollection()->map(function ($i) {
            $code = $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT));
            $priority = strtolower($i->priority ?? $i->priorite ?? '');
            $isReopened = $i->logs()->whereIn('action', ['close_rejected','reopened'])->exists();
            $show = route('resolveur.incidents.show', $i);
            $priorityUrl = Route::has('resolveur.incidents.priority') ? route('resolveur.incidents.priority', $i) : url('/resolveur/incidents/'.$i->id.'/priority');
            $escalateUrl = Route::has('resolveur.incidents.escalate') ? route('resolveur.incidents.escalate', $i) : url('/resolveur/incidents/'.$i->id.'/escalate');

            return [
                'id' => $i->id,
                'public_id' => $code,
                'titre' => $i->titre ?? '',
                'priority' => $priority,
                'statut' => $i->statut ?? '',
                'sla_due_at' => optional($i->sla_due_at)->format('Y-m-d H:i'),
                'escalation_level' => (int) ($i->escalation_level ?? 0),
                'assigned_to' => optional($i->assignedUser)->name,
                'created_by' => optional($i->utilisateur)->name,
                'created_at' => optional($i->created_at)->format('Y-m-d H:i'),
                'is_reopened' => $isReopened,
                'urls' => [
                    'show_resolveur' => $show,
                    'priority_resolveur' => $priorityUrl,
                    'escalate_resolveur' => $escalateUrl,
                ],
            ];
        });

        return response()->json([
            'data' => $data->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        $q = Incident::query();

        if ($request->query('assigned') === 'me') {
            $q->where('attribue_a', $user->id);
        }

        $statut = $request->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($request->query('priority', $request->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        if (in_array($request->query('reopened'), ['1','true','yes','on'], true)) {
            $q->whereHas('logs', function($l){
                $l->whereIn('action', ['close_rejected','reopened']);
            });
        }

        $de = $request->query('from');
        $a  = $request->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $request->query('q', $request->query('text', '')));
        if ($terme !== '') {
            $termeSansEspaces = preg_replace('/\s+/', '', $terme);
            $termeUpper = strtoupper($termeSansEspaces);
            $chiffres = preg_replace('/\D+/', '', $termeUpper);
            $idNum = $chiffres !== '' ? (int) ltrim($chiffres, '0') : null;

            $q->where(function ($qq) use ($terme, $idNum) {
                $qq->where('titre', 'like', '%'.$terme.'%')
                   ->orWhere('description', 'like', '%'.$terme.'%')
                   ->orWhere('public_id', 'like', '%'.$terme.'%');
                if ($idNum !== null && $idNum > 0) {
                    $qq->orWhere('id', $idNum);
                }
            });
        }

        $q->with(['assignedUser','utilisateur']);

        $rows = $q->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Code','Titre','Priorité','Statut','Assigné à','Créé par','Créé','SLA','Réouvert']);

        foreach ($rows as $i) {
            $code   = $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT));
            $prio   = ucfirst(strtolower($i->priority ?? ''));
            $assign = $i->assignedUser->name ?? '';
            $usernm = $i->utilisateur->name ?? '';
            $sla    = $i->sla_due_at ? (now()->lte($i->sla_due_at) ? 'OK' : 'Breach') : '—';
            $reo    = $i->logs()->whereIn('action', ['close_rejected','reopened'])->exists() ? 'Oui' : 'Non';

            fputcsv($handle, [
                $code,
                $i->titre,
                $prio,
                Incident::labelForStatus($i->statut),
                $assign,
                $usernm,
                optional($i->created_at)->format('Y-m-d H:i'),
                $sla,
                $reo,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="incidents-resolveur.csv"',
        ]);
    }

    public function show(Incident $incident)
    {
        $incident->load(['assignedUser', 'logs', 'commentaires.auteur']);
        return view('resolveur.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $incident->load(['assignedUser', 'logs', 'commentaires.auteur']);
        $resolveurs = User::where('role', 'resolveur')->orderBy('name')->get(['id','name']);
        return view('resolveur.incidents.edit', compact('incident', 'resolveurs'));
    }

    public function setStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'statut' => 'required|string|in:nouveau,en_cours,résolu,fermé',
        ]);

        $incident->setStatusWithLog($request->statut);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function setPriority(Request $request, Incident $incident)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        $incident->priority = $request->priority;

        if (Schema::hasColumn('incidents', 'priorite')) {
            $incident->priorite = ucfirst($request->priority);
        }

        if (Schema::hasColumn('incidents', 'sla_due_at')) {
            $hours = (int) config('itil.sla_hours.' . $incident->priority, 48);
            $incident->sla_due_at = now()->addHours($hours);
        }

        $incident->save();

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => Auth::id(),
            'action'      => 'priority_changed',
            'details'     => 'Priorité: ' . ($incident->priorite ?? $incident->priority),
        ]);

        return back()->with('success', 'Priorité mise à jour.');
    }

    public function escalate(Request $request, Incident $incident)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'resolveur', 403);

        $request->validate([
            'to_resolveur_id' => ['required','integer','exists:users,id'],
        ]);

        $to = User::where('id', $request->integer('to_resolveur_id'))->where('role', 'resolveur')->first();

        if (! $to) {
            return back()->withErrors(['to_resolveur_id' => 'Le destinataire doit être un résolveur valide.']);
        }

        if ((int) $incident->attribue_a === (int) $to->id) {
            return back()->with('success', 'Incident déjà assigné à ce résolveur.');
        }

        $fromUser = $incident->assignedUser;
        $fromName = $fromUser?->name ?? 'Non assigné';

        $incident->attribue_a = $to->id;

        if (Schema::hasColumn('incidents', 'escalation_level')) {
            $incident->escalation_level = (int) ($incident->escalation_level ?? 0) + 1;
        }

        $statutNouveau = defined(Incident::class.'::STATUT_NOUVEAU') ? Incident::STATUT_NOUVEAU : 'nouveau';
        $statutEnCours = defined(Incident::class.'::STATUT_EN_COURS') ? Incident::STATUT_EN_COURS : 'en_cours';
        if (($incident->statut ?? null) === $statutNouveau) {
            $incident->statut = $statutEnCours;
        }

        $incident->save();

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => $user->id,
            'action'      => 'escalated',
            'details'     => sprintf('Réassigné de %s à %s', $fromName, $to->name),
        ]);

        return back()->with('success', 'Incident réassigné à ' . $to->name . '.');
    }

    public function comment(Request $request, Incident $incident)
    {
        $request->validate([
            'commentaire' => ['required','string','max:5000'],
        ]);

        if (class_exists(\App\Models\IncidentComment::class)) {
            \App\Models\IncidentComment::create([
                'incident_id' => $incident->id,
                'user_id'     => Auth::id(),
                'commentaire' => $request->commentaire,
            ]);
        } else {
            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => Auth::id(),
                'action'      => 'comment',
                'details'     => $request->commentaire,
            ]);
        }

        return back()->with('success', 'Commentaire ajouté.');
    }
}
