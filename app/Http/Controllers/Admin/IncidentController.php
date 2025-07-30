<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    // Mostrar todas las incidencias con nombre del usuario
    public function index()
    {
        $incidents = Incident::latest()->with('utilisateur')->paginate(10);
        return view('admin.incidents.index', compact('incidents'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $users = User::where('role', 'utilisateur')->get(); // Solo usuarios normales
        return view('admin.incidents.create', compact('users'));
    }

    // Guardar nueva incidencia
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'required',
            'utilisateur_id' => 'required|exists:users,id',
        ]);

        Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'nouveau', // Siempre empieza como "nouveau"
            'utilisateur_id' => $request->utilisateur_id,
        ]);

        return redirect()->route('admin.incidents.index')->with('success', 'Incident créé avec succès.');
    }

    // Mostrar formulario de edición
    public function edit(Incident $incident)
    {
        return view('admin.incidents.edit', compact('incident'));
    }

    // Actualizar incidencia
    public function update(Request $request, Incident $incident)
    {
        $request->validate([
            'titre' => 'required',
            'description' => 'required',
            'statut' => 'required|in:nouveau,en_cours,résolu',
        ]);

        $incident->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => $request->statut,
        ]);

        return redirect()->route('admin.incidents.index')->with('success', 'Incident mis à jour.');
    }

    // Eliminar incidencia
    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('admin.incidents.index')->with('success', 'Incident supprimé.');
    }
}
