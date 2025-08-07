<?php

namespace App\Http\Controllers\Resolveur;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\IncidentStatutUpdated;

class ResolveurIncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with(['utilisateur', 'gestionnaire'])->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->boolean('assigne_a_moi')) {
            $query->where('attribue_a', Auth::id());
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $incidents = $query->paginate(10);

        return view('resolveur.incidents.index', compact('incidents'));
    }

    public function create()
    {
        $users = User::where('role', 'utilisateur')->get();
        return view('resolveur.incidents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
            'utilisateur_id' => 'required|exists:users,id',
            'attribue_a' => 'nullable|exists:users,id',
        ]);

        Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => 'nouveau',
            'utilisateur_id' => $request->utilisateur_id,
            'attribue_a' => $request->attribue_a,
        ]);

        return redirect()->route('resolveur.incidents.index')->with('success', 'Incident créé avec succès.');
    }

    public function show(Incident $incident)
    {
        $incident->load(['utilisateur', 'gestionnaire', 'commentaires.auteur']);
        return view('resolveur.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $resolveurs = User::where('role', 'resolveur')->get();
        $incident->load(['utilisateur', 'commentaires.auteur']);
        return view('resolveur.incidents.edit', compact('incident', 'resolveurs'));
    }

    public function update(Request $request, Incident $incident)
    {
        $request->validate([
            'statut' => 'required|in:nouveau,en_cours,résolu',
            'commentaire' => 'nullable|string',
            'attribue_a' => 'nullable|exists:users,id',
        ]);

        $ancienStatut = $incident->statut;

        $incident->update([
            'statut' => $request->statut,
            'attribue_a' => $request->attribue_a,
        ]);

        if ($incident->utilisateur && $ancienStatut !== $incident->statut) {
            $incident->utilisateur->notify(new IncidentStatutUpdated($incident));
        }

        if ($request->filled('commentaire')) {
            IncidentComment::create([
                'incident_id' => $incident->id,
                'user_id' => Auth::id(),
                'commentaire' => $request->commentaire,
            ]);
        }

        return redirect()->route('resolveur.incidents.index')->with('success', 'Incident mis à jour avec succès.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('resolveur.incidents.index')->with('success', 'Incident supprimé.');
    }

    public function exportCsv(Request $request)
    {
        $query = Incident::with(['utilisateur', 'gestionnaire'])->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->boolean('assigne_a_moi')) {
            $query->where('attribue_a', Auth::id());
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $incidents = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="incidents_resolveur.csv"',
        ];

        $callback = function () use ($incidents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Titre', 'Statut', 'Utilisateur', 'Gestionnaire']);

            foreach ($incidents as $incident) {
                fputcsv($handle, [
                    $incident->id,
                    $incident->titre,
                    $incident->statut,
                    $incident->utilisateur?->name,
                    $incident->gestionnaire?->name,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
