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

    public function index(Request $request)
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

        $incidents = $query->paginate(10);
        $typesDisponibles = Incident::distinct()->pluck('type')->filter()->values();

        return view('utilisateur.incidents.index', compact('incidents', 'typesDisponibles'));
    }

    public function create(Request $request)
    {
        $categorie = $request->input('categorie');

        $types = match ($categorie) {
            'communication' => [
                'Messagerie' => "Difficultés d'accès ou d'envoi/réception via la messagerie institutionnelle (email universitaire). Merci d'indiquer l'adresse concernée et le type d'erreur rencontré.",
                'Outils collaboratifs' => "Problème avec des outils collaboratifs tels que le drive partagé, calendrier, documents collaboratifs. Veuillez préciser l’outil concerné et le type de dysfonctionnement.",
            ],
            'acces' => [
                'Connexion Internet' => "Accès limité ou inexistant au réseau câblé ou Wi-Fi. Indiquez l’emplacement exact (bâtiment/salle), le matériel utilisé, si le problème est isolé ou général, et tout message d’erreur reçu.",
                'Problèmes de mot de passe' => "Connexion impossible à cause d’un mot de passe oublié ou erroné. Essayez de le réinitialiser via le portail. Si cela ne fonctionne pas, indiquez votre identifiant et la date du dernier accès fonctionnel.",
            ],
            'plateformes' => [
                'Formulaires en ligne' => "Erreur lors de la soumission ou de l'affichage de formulaires administratifs ou pédagogiques. Précisez le nom du formulaire, l’URL, le message d’erreur, et votre navigateur.",
                'Sites web universitaires' => "Site web inaccessible ou éléments non fonctionnels. Mentionnez l’URL exacte, l’heure du problème, et la nature de la panne (chargement lent, page blanche, etc.).",
            ],
            'equipements' => [
                'Matériel défectueux' => "Problème matériel (ordinateur, imprimante, scanner...). Décrivez le type de matériel, le numéro d’inventaire si possible, le lieu d’installation, et les symptômes de la panne.",
                'Logiciels manquants' => "Un logiciel requis est absent de votre poste. Indiquez le nom exact du logiciel, l’usage prévu, le système d’exploitation, et si une version spécifique est requise.",
                'Problème de licence' => "Un logiciel signale un problème de licence (licence expirée, non reconnue, activation impossible). Merci d’indiquer le logiciel, la version, et le message d’erreur affiché.",
            ],
            'enseignement' => [
                'Équipements de labo' => "Défaillance d’un appareil dans un laboratoire ou atelier pédagogique. Précisez l’équipement, le local, l’horaire et la nature du dysfonctionnement rencontré.",
                'Accès à bases de données' => "Impossibilité d’accès à des ressources scientifiques (Scopus, Web of Science, etc.). Indiquez la base concernée, le message d’erreur, et votre mode d’accès (VPN, campus, proxy...).",
            ],
            'assistance' => [
                'Demande d’assistance' => "Vous avez besoin d’une aide pour une tâche numérique spécifique. Décrivez votre besoin le plus précisément possible afin d’orienter l’intervention.",
                'Orientation numérique' => "Demande d’accompagnement pour l’utilisation des outils numériques (connexion, usage de Moodle, etc.). Précisez vos objectifs et les blocages rencontrés.",
                'Autres demandes' => "Votre demande ne correspond à aucune catégorie listée. Merci de détailler votre situation, le service concerné, et les actions déjà tentées.",
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

        return redirect()->route('utilisateur.incidents.show', $incident)->with('success', 'Incident créé avec succès.');
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'Impossible de modifier un incident déjà traité.');
        }

        return view('utilisateur.incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== 'nouveau') {
            return redirect()->route('utilisateur.incidents.index')
                ->with('error', 'Impossible de mettre à jour un incident déjà traité.');
        }

        $request->validate([
            'titre' => 'required|string',
            'description' => 'required|string',
        ]);

        $incident->update([
            'titre' => $request->titre,
            'description' => $request->description,
        ]);

        return redirect()->route('utilisateur.incidents.index')
            ->with('success', 'Incident mis à jour avec succès.');
    }

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

    public function show(Incident $incident)
    {
        $user = Auth::user();

        if (!$user->estAdmin() && $incident->utilisateur_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $incident->load(['utilisateur', 'gestionnaire', 'commentaires.auteur']);

        if ($user->estAdmin()) {
            return view('admin.incidents.show', compact('incident'));
        }

        return view('utilisateur.incidents.show', compact('incident'));
    }
}
