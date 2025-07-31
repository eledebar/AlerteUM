<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\IncidentStatutUpdated;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with('utilisateur')->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $incidents = $query->paginate(10);

        return view('admin.incidents.index', compact('incidents'));
    }

    public function create()
    {
        $users = User::where('role', 'utilisateur')->get();
        return view('admin.incidents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'nouveau',
            'utilisateur_id' => $request->utilisateur_id,
        ]);

        return redirect()->route('admin.incidents.index')->with('success', 'Incident créé avec succès.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['utilisateur', 'commentaires.auteur']);
        return view('admin.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $incident->load(['utilisateur', 'commentaires.auteur']);
        return view('admin.incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
{
    $request->validate([
        'titre' => 'required|string',
        'description' => 'required|string',
        'statut' => 'required|in:nouveau,en_cours,résolu',
        'commentaire' => 'nullable|string',
    ]);

    // Detectar si ha cambiado el statut
    $ancienStatut = $incident->statut;

    $incident->update([
        'titre' => $request->titre,
        'description' => $request->description,
        'statut' => $request->statut,
    ]);

    // Notificar al usuario si se ha cambiado el statut
    if ($incident->utilisateur && $ancienStatut !== $incident->statut) {
        $incident->utilisateur->notify(new \App\Notifications\IncidentStatutUpdated($incident));
    }

    // Comentario opcional
    if ($request->filled('commentaire')) {
        \App\Models\IncidentComment::create([
            'incident_id' => $incident->id,
            'user_id' => Auth::id(),
            'commentaire' => $request->commentaire,
        ]);
    }

    return redirect()->route('admin.incidents.index')->with('success', 'Incident mis à jour avec succès.');
}

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('admin.incidents.index')->with('success', 'Incident supprimé.');
    }
}
