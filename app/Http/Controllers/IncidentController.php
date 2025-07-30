<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    // Liste des incidents (filtrée par utilisateur sauf admin/gestionnaire)
    public function index(Request $request)
{
    $user = Auth::user();

    $query = Incident::where('utilisateur_id', $user->id)->latest();

    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }

    $incidents = $query->paginate(10);

    return view('utilisateur.incidents.index', compact('incidents'));
}


    // Formulaire de création
    public function create()
    {
        return view('utilisateur.incidents.create');
    }

    // Enregistrement d’un incident
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'required',
            // NO pedimos 'statut' porque lo forzamos a 'nouveau'
        ]);

        Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'nouveau', // Se establece por defecto
            'utilisateur_id' => Auth::id(),
        ]);

        return redirect()->route('utilisateur.incidents.index')
                         ->with('success', 'Incident créé avec succès.');
    }

    // Formulaire de modification
    // Formulaire de modification
public function edit(Incident $incident)
{
    $this->authorize('update', $incident);

    if ($incident->statut !== 'nouveau') {
        return redirect()->route('utilisateur.incidents.index')
            ->with('error', 'Impossible de modifier une incidence déjà traitée.');
    }

    return view('utilisateur.incidents.edit', compact('incident'));
}
    // Mise à jour de l’incident
   // Mise à jour
public function update(Request $request, Incident $incident)
{
    $this->authorize('update', $incident);

    if ($incident->statut !== 'nouveau') {
        return redirect()->route('utilisateur.incidents.index')
            ->with('error', 'Impossible de mettre à jour cette incidence.');
    }

    $request->validate([
        'titre' => 'required',
        'description' => 'required',
    ]);

    $incident->update([
        'titre' => $request->titre,
        'description' => $request->description,
    ]);

    return redirect()->route('utilisateur.incidents.index')
        ->with('success', 'Incident mis à jour.');
}


    // Suppression
   public function destroy(Incident $incident)
{
    $this->authorize('delete', $incident);

    if ($incident->statut !== 'nouveau') {
        return redirect()->route('utilisateur.incidents.index')
            ->with('error', 'Impossible de supprimer une incidence déjà en cours ou résolue.');
    }

    $incident->delete();

    return redirect()->route('utilisateur.incidents.index')
        ->with('success', 'Incident supprimé.');
}

    public function show(Incident $incident)
{
    $user = Auth::user();

    // Asegurarse que el usuario es el propietario o admin/gestionnaire
    if (!$user->estAdminOuGestionnaire() && $incident->utilisateur_id !== $user->id) {
        abort(403); // Prohibido
    }

    $incident->load('commentaires.auteur');

    return view('utilisateur.incidents.show', compact('incident'));
}

}
