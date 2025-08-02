<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentComment;
use App\Notifications\IncidentStatutUpdated;
use App\Notifications\NouvelleIncidentCree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->estAdmin()
            ? Incident::with(['utilisateur', 'gestionnaire'])->latest()
            : Incident::where('utilisateur_id', $user->id)->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($user->estAdmin() && $request->boolean('assigne_a_moi')) {
            $query->where('attribue_a', Auth::id());
        }

        if (!$user->estAdmin()) {
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('titre')) {
                $query->where(function ($q) use ($request) {
                    $q->where('titre', 'like', '%' . $request->titre . '%')
                      ->orWhere('statut', 'like', '%' . $request->titre . '%');
                });
            }
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $incidents = $query->paginate(10);
        $typesDisponibles = $user->estAdmin() ? [] : Incident::distinct()->pluck('type')->filter()->values();

        return $user->estAdmin()
            ? view('admin.incidents.index', compact('incidents'))
            : view('utilisateur.incidents.index', compact('incidents', 'typesDisponibles'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $query = Incident::where('utilisateur_id', $user->id)->latest();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('titre')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->titre . '%')
                  ->orWhere('statut', 'like', '%' . $request->titre . '%');
            });
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
            'Content-Disposition' => 'attachment; filename="incidents_utilisateur.csv"',
        ];

        $callback = function () use ($incidents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Titre', 'Statut', 'Type']);
            foreach ($incidents as $incident) {
                fputcsv($handle, [
                    $incident->id,
                    $incident->titre,
                    $incident->statut,
                    $incident->type,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(Request $request)
    {
        $categorie = $request->input('categorie');

        $types = match ($categorie) {
            'communication' => [
                'Messagerie' => "Difficultés d'accès ou d'envoi/réception via la messagerie institutionnelle...",
                'Outils collaboratifs' => "Problème avec des outils collaboratifs tels que le drive partagé...",
            ],
            'acces' => [
                'Connexion Internet' => "Accès limité ou inexistant au réseau câblé ou Wi-Fi...",
                'Problèmes de mot de passe' => "Connexion impossible à cause d’un mot de passe oublié ou erroné...",
            ],
            'plateformes' => [
                'Formulaires en ligne' => "Erreur lors de la soumission ou de l'affichage de formulaires...",
                'Sites web universitaires' => "Site web inaccessible ou éléments non fonctionnels...",
            ],
            'equipements' => [
                'Matériel défectueux' => "Problème matériel (ordinateur, imprimante...)",
                'Logiciels manquants' => "Un logiciel requis est absent de votre poste...",
                'Problème de licence' => "Un logiciel signale un problème de licence...",
            ],
            'enseignement' => [
                'Équipements de labo' => "Défaillance d’un appareil dans un laboratoire ou atelier...",
                'Accès à bases de données' => "Impossibilité d’accès à des ressources scientifiques...",
            ],
            'assistance' => [
                'Demande d’assistance' => "Vous avez besoin d’une aide pour une tâche numérique...",
                'Orientation numérique' => "Demande d’accompagnement pour l’utilisation des outils numériques...",
                'Autres demandes' => "Votre demande ne correspond à aucune catégorie listée...",
            ],
            default => [],
        };

        return view('utilisateur.incidents.create', [
            'categorie' => $categorie,
            'types' => $types,
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->estAdmin()) {
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

            return redirect()->route('admin.incidents.index')->with('success', 'Incident créé avec succès.');
        }

        // Usuario normal
        $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'categorie' => 'required|string',
            'type' => 'required|string',
        ]);

        $incident = Incident::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'categorie' => $request->categorie,
            'type' => $request->type,
            'statut' => 'nouveau',
            'utilisateur_id' => Auth::id(),
        ]);

        // 🔔 Notificar a todos los administradores
        User::where('role', 'admin')->get()->each(function ($admin) use ($incident) {
            $admin->notify(new NouvelleIncidentCree($incident));
        });

        return redirect()->route('utilisateur.incidents.show', $incident)->with('success', 'Incident créé avec succès.');
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        if (Auth::user()->estAdmin()) {
            $incident->load(['utilisateur', 'commentaires.auteur']);
            $admins = User::where('role', 'admin')->get();
            return view('admin.incidents.edit', compact('incident', 'admins'));
        }

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')->with('error', 'Impossible de modifier un incident déjà traité.');
        }

        return view('utilisateur.incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        if (Auth::user()->estAdmin()) {
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

            return redirect()->route('admin.incidents.index')->with('success', 'Incident mis à jour avec succès.');
        }

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')->with('error', 'Impossible de mettre à jour un incident déjà traité.');
        }

        $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
        ]);

        $incident->update([
            'titre' => $request->titre,
            'description' => $request->description,
        ]);

        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident mis à jour avec succès.');
    }

    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->back()->with('error', 'Impossible de supprimer un incident déjà traité.');
        }

        $incident->delete();

        $route = Auth::user()->estAdmin() ? 'admin.incidents.index' : 'utilisateur.incidents.index';

        return redirect()->route($route)->with('success', 'Incident supprimé avec succès.');
    }

    public function show(Incident $incident)
    {
        $user = Auth::user();

        if (!$user->estAdmin() && $incident->utilisateur_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $incident->load(['utilisateur', 'gestionnaire', 'commentaires.auteur']);

        return $user->estAdmin()
            ? view('admin.incidents.show', compact('incident'))
            : view('utilisateur.incidents.show', compact('incident'));
    }
}
