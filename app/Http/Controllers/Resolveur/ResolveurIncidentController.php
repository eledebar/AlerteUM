<?php

namespace App\Http\Controllers\Resolveur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 10;

    $incidents = Incident::query()
        ->filter($request, $user)
        ->sorted($request)
        ->with(['assignedUser','lastLog'])   
        ->paginate($perPage)
        ->withQueryString();

    return view('resolveur.incidents.index', compact('incidents'));
}


    public function show(Incident $incident)
    {
        $incident->load(['assignedUser', 'logs', 'commentaires.auteur']);

        return view('resolveur.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $incident->load(['assignedUser', 'logs', 'commentaires.auteur']);

        $resolveurs = User::where('role', 'resolveur')
            ->orderBy('name')
            ->get(['id','name']);

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

        $to = User::where('id', $request->integer('to_resolveur_id'))
            ->where('role', 'resolveur')
            ->first();

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
