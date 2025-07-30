<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    // Liste des incidents (filtrée par utilisateur sauf admin/gestionnaire)
    public function index()
    {
        $user = Auth::user();

        if ($user->estAdminOuGestionnaire()) {
            $incidents = Incident::latest()->paginate(10);
        } else {
            $incidents = Incident::where('utilisateur_id', $user->id)
                                 ->latest()
                                 ->paginate(10);
        }

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
    public function edit(Incident $incident)
    {
        return view('utilisateur.incidents.edit', compact('incident'));
    }

    // Mise à jour de l’incident
   public function update(Request $request, Incident $incident)
{
    $request->validate([
        'titre' => 'required',
        'description' => 'required',
    ]);

    // Solo actualizamos le titre y la description, nunca el statut aquí
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
        $incident->delete();

        return redirect()->route('utilisateur.incidents.index')
                         ->with('success', 'Incident supprimé.');
    }
}
