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
            'Messagerie' => "Vous rencontrez des difficultés avec la messagerie institutionnelle (problèmes d'envoi, de réception, d'accès ou de configuration). Veuillez préciser le type de problème rencontré, la plateforme utilisée (web, application, etc.) et indiquer votre adresse e-mail institutionnelle.",
            'Outils collaboratifs' => "Problèmes liés à l'utilisation d'outils collaboratifs tels que les drives partagés, calendriers ou plateformes de travail en équipe. Merci d’indiquer l’outil concerné, la nature du problème (accès, partage, synchronisation...) et si d’autres utilisateurs sont également impactés.",
        ],
        'acces' => [
            'Connexion Internet' => "Connexion Internet absente, instable ou très lente via le réseau Wi-Fi ou filaire de l’établissement. Merci de préciser l’endroit où le problème survient (bâtiment, salle...), l’appareil utilisé et les horaires approximatifs où la déconnexion se produit.",
            'Problèmes de mot de passe' => "Vous ne parvenez pas à accéder à un service en raison d’un mot de passe oublié, erroné ou expiré. Veuillez indiquer le service concerné (ENT, messagerie, plateforme...) et si vous avez déjà tenté de réinitialiser le mot de passe. N'oubliez pas d'indiquer votre identifiant universitaire si possible.",
        ],
        'plateformes' => [
            'Formulaires en ligne' => "Vous rencontrez une erreur lors de l’affichage ou de la soumission d’un formulaire en ligne (champs inactifs, validation impossible, données perdues...). Merci d’indiquer le lien du formulaire, le service concerné et le moment où l’erreur est apparue.",
            'Sites web universitaires' => "L’un des sites web universitaires est inaccessible ou certaines de ses fonctionnalités ne répondent pas. Veuillez indiquer l’URL concernée, ce que vous essayiez de faire et le type d'appareil utilisé (ordinateur, smartphone, etc.).",
        ],
        'equipements' => [
            'Matériel défectueux' => "Un appareil (ordinateur, imprimante, projecteur...) est en panne ou ne fonctionne pas correctement. Merci d’indiquer le type de matériel, son emplacement (salle, bâtiment...), les symptômes du dysfonctionnement et si des tentatives de redémarrage ont été faites.",
            'Logiciels manquants' => "Un logiciel nécessaire à votre activité n’est pas installé sur votre poste. Merci d’indiquer le nom exact du logiciel, son utilisation prévue et le type de poste concerné (fixe, portable, usage personnel ou partagé).",
            'Problème de licence' => "Un logiciel indique un problème de licence (activation échouée, licence expirée ou invalide). Merci de préciser le nom du logiciel, le message d’erreur reçu et l’impact sur votre activité (utilisation partielle ou impossible).",
        ],
        'enseignement' => [
            'Équipements de labo' => "Un équipement présent dans un laboratoire ou un atelier ne fonctionne pas ou nécessite une intervention technique. Merci d’indiquer le nom de l’appareil, son emplacement et en quoi cela empêche la réalisation des activités pédagogiques.",
            'Accès à bases de données' => "Vous ne parvenez pas à accéder à des bases de données ou des ressources scientifiques en ligne. Veuillez indiquer le nom de la ressource, la méthode d’accès utilisée (via ENT, VPN, etc.) et si le problème est constant ou intermittent.",
        ],
        'assistance' => [
            'Demande d’assistance' => "Vous souhaitez être aidé(e) pour accomplir une tâche numérique précise (utilisation d’un outil, configuration d’un service, résolution d’un problème technique...). Merci de décrire votre besoin et le contexte d’utilisation.",
            'Orientation numérique' => "Vous demandez un accompagnement ou une formation pour mieux utiliser les outils numériques de l’établissement. Merci d’indiquer les outils concernés, votre rôle dans l’établissement (étudiant, enseignant, personnel...) et vos attentes.",
            'Autres demandes' => "Votre demande ne correspond à aucune des catégories précédentes mais concerne un service ou outil numérique. Merci de décrire clairement votre situation, le besoin identifié et d’indiquer vos coordonnées de contact (email, rôle, etc.).",
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
