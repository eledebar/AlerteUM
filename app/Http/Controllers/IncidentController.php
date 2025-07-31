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

    // Lista de incidencias del usuario conectado
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

    // Formulario de creación
    public function create()
    {
        return view('utilisateur.incidents.create');
    }

    // Guardar una nueva incidencia y notificar a los admins
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

    // Notificar a todos los administradores
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new NouvelleIncidentCree($incident));
    }

    return redirect()->route('utilisateur.incidents.index')
        ->with('success', 'Incident créé avec succès.');
}
    // Formulario de edición
    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'No se puede modificar una incidencia ya tratada.');
        }

        return view('utilisateur.incidents.edit', compact('incident'));
    }

    // Actualizar una incidencia
    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'No se puede actualizar una incidencia ya tratada.');
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
            ->with('success', 'Incidencia actualizada.');
    }

    // Eliminar
    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'No se puede eliminar una incidencia ya tratada.');
        }

        $incident->delete();

        return redirect()->route('utilisateur.incidents.index')
            ->with('success', 'Incidencia eliminada.');
    }

    // Mostrar detalle (usuario o admin)
    public function show(Incident $incident)
    {
        $user = Auth::user();

        // Permitir solo si es admin o dueño
        if (!$user->estAdmin() && $incident->utilisateur_id !== $user->id) {
            abort(403, 'Acceso no autorizado.');
        }

        $incident->load('commentaires.auteur');

        // Mostrar la vista correspondiente
        if ($user->estAdmin()) {
            return view('admin.incidents.show', compact('incident'));
        }

        return view('utilisateur.incidents.show', compact('incident'));
    }
}
