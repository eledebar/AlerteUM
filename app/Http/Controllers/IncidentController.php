<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    // Liste des incidents
    public function index()
    {
        $incidents = Incident::latest()->paginate(10);
        return view('utilisateur.incidents.index', compact('incidents'));
    }

    // Formulaire de création
    public function create()
    {
        return view('utilisateur.incidents.create');
    }

    // Stockage d’un nouvel incident
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'required',
            'statut' => 'required',
        ]);

        Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => $request->statut,
            'utilisateur_id' => Auth::id(),
        ]);

        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident créé avec succès.');
    }

    // Formulaire d’édition
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
            'statut' => 'required',
        ]);

        $incident->update($request->all());

        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident mis à jour.');
    }

    // Suppression
    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident supprimé.');
    }
}
