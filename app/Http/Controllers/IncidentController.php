<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Notifications\NouvelleIncidentCree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    // Liste des incidents de l'utilisateur connecté
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Incident::where('utilisateur_id', $user->id)->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        $incidents = $query->paginate(10);

        return view('utilisateur.incidents.index', compact('incidents'));
    }

    // Formulaire de création
    public function create()
    {
        return view('utilisateur.incidents.create');
    }

    // Enregistrer un nouvel incident et notifier les admins
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'required',
        ]);

        $incident = Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'nouveau',
            'utilisateur_id' => Auth::id(),
        ]);

        // Notifier tous les administrateurs
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NouvelleIncidentCree($incident));
        }

        return redirect()->route('utilisateur.incidents.index')
            ->with('success', 'Incident créé avec succès.');
    }

    // Formulaire d’édition
    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'Impossible de modifier un incident déjà traité.');
        }

        return view('utilisateur.incidents.edit', compact('incident'));
    }

    // Mettre à jour un incident
    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'Impossible de mettre à jour un incident déjà traité.');
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
            ->with('success', 'Incident mis à jour avec succès.');
    }

    // Supprimer
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'Impossible de supprimer un incident déjà traité.');
        }

        $incident->delete();

        return redirect()->route('utilisateur.incidents.index')
            ->with('success', 'Incident supprimé avec succès.');
    }

    // Afficher les détails (utilisateur ou admin)
    public function show(Incident $incident)
    {
        $user = Auth::user();

        // Autorisé seulement si admin ou propriétaire
        if (!$user->estAdmin() && $incident->utilisateur_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $incident->load('commentaires.auteur');

        // Afficher la vue correspondante
        if ($user->estAdmin()) {
            return view('admin.incidents.show', compact('incident'));
        }

        return view('utilisateur.incidents.show', compact('incident'));
    }
}
