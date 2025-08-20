<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentComment;
use App\Models\IncidentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Notifications\IncidentReopenedByUser;
use App\Notifications\IncidentCreated;

class UserIncidentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $q = Incident::query()->where('utilisateur_id', $user->id);

        $statut = $request->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($request->query('priority', $request->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        $de = $request->query('from');
        $a  = $request->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $request->query('q', ''));
        if ($terme !== '') {
            $termeSansEspaces = preg_replace('/\s+/', '', $terme);
            $termeUpper = strtoupper($termeSansEspaces);
            $chiffres = preg_replace('/\D+/', '', $termeUpper);
            $idNum = $chiffres !== '' ? (int) ltrim($chiffres, '0') : null;

            $q->where(function ($qq) use ($terme, $idNum) {
                $qq->where('titre', 'like', '%'.$terme.'%')
                   ->orWhere('description', 'like', '%'.$terme.'%')
                   ->orWhere('public_id', 'like', '%'.$terme.'%');
                if ($idNum !== null && $idNum > 0) {
                    $qq->orWhere('id', $idNum);
                }
            });
        }

        $trisAutorises = ['created_at','priority','priorite','statut','escalation_level','sla_due_at','public_id','titre','id'];
        $triDemande = $request->query('sort');
        if ($triDemande === 'priorite' && !Schema::hasColumn('incidents', 'priorite')) {
            $triDemande = 'priority';
        }
        $tri = in_array($triDemande, $trisAutorises, true) ? $triDemande : 'created_at';
        $dir = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($tri, $dir);

        $q->with(['assignedUser','utilisateur','lastLog']);

        $incidents = $q->paginate(10)->withQueryString();

        $typesDisponibles = Incident::where('utilisateur_id', $user->id)->distinct()->pluck('type')->filter()->values();

        return view('utilisateur.incidents.index', compact('incidents','typesDisponibles'));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        $q = Incident::query()->where('utilisateur_id', $user->id);

        $statut = $request->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($request->query('priority', $request->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        $de = $request->query('from');
        $a  = $request->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $request->query('q', ''));
        if ($terme !== '') {
            $termeSansEspaces = preg_replace('/\s+/', '', $terme);
            $termeUpper = strtoupper($termeSansEspaces);
            $chiffres = preg_replace('/\D+/', '', $termeUpper);
            $idNum = $chiffres !== '' ? (int) ltrim($chiffres, '0') : null;

            $q->where(function ($qq) use ($terme, $idNum) {
                $qq->where('titre', 'like', '%'.$terme.'%')
                   ->orWhere('description', 'like', '%'.$terme.'%')
                   ->orWhere('public_id', 'like', '%'.$terme.'%');
                if ($idNum !== null && $idNum > 0) {
                    $qq->orWhere('id', $idNum);
                }
            });
        }

        $q->with(['assignedUser','utilisateur']);
        $rows = $q->get();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Code','Titre','Priorité','Statut','Assigné à','Créé','SLA']);

        foreach ($rows as $i) {
            $code   = $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT));
            $prio   = ucfirst(strtolower($i->priority ?? ''));
            $assign = $i->assignedUser->name ?? '';
            $sla    = $i->sla_due_at ? (now()->lte($i->sla_due_at) ? 'OK' : 'Breach') : '—';

            fputcsv($handle, [
                $code,
                $i->titre,
                $prio,
                Incident::labelForStatus($i->statut),
                $assign,
                optional($i->created_at)->format('Y-m-d H:i'),
                $sla,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mes-incidents.csv"',
        ]);
    }

    public function create(Request $request)
    {
        $categorie = $request->input('categorie');

        $types = match ($categorie) {
            'communication' => [
                'Messagerie'            => "Difficultés avec la messagerie institutionnelle (envoi/réception/accès/config). Indiquez la plateforme et votre adresse institutionnelle.",
                'Outils collaboratifs'  => "Problèmes avec drive partagé, calendrier, Teams, etc. Indiquez l’outil, la nature du problème et si d’autres sont impactés.",
            ],
            'acces' => [
                'Connexion Internet'    => "Connexion absente/instable/lente via Wi-Fi/Ethernet. Indiquez bâtiment/salle, appareil et horaires approximatifs de coupure.",
                'Problèmes de mot de passe' => "Accès impossible (mot de passe oublié/expiré). Indiquez le service (ENT, mail…) et si vous avez tenté la réinitialisation.",
            ],
            'plateformes' => [
                'Formulaires en ligne'  => "Erreur à l’affichage/soumission (champs inactifs, validation…). Indiquez le lien, le service et le moment du bug.",
                'Sites web universitaires' => "Site injoignable ou fonctions KO. Indiquez l’URL et l’appareil (PC/mobile).",
            ],
            'equipements' => [
                'Matériel défectueux'   => "Panne sur ordinateur/imprimante/vidéoprojecteur… Indiquez type, emplacement et symptômes.",
                'Logiciels manquants'   => "Logiciel nécessaire non installé. Indiquez nom, usage prévu et type de poste.",
                'Problème de licence'   => "Erreur de licence. Indiquez logiciel, message exact et impact.",
            ],
            'enseignement' => [
                'Équipements de labo'   => "Équipement de labo en panne. Indiquez nom, emplacement et impact pédagogique.",
                'Accès à bases de données' => "Accès impossible à une ressource. Indiquez ressource et méthode.",
            ],
            'assistance' => [
                'Demande d’assistance'  => "Aide pour accomplir une tâche. Décrivez le contexte.",
                'Orientation numérique' => "Accompagnement ou formation.",
                'Autres demandes'       => "Autre besoin numérique.",
            ],
            default => [],
        };

        return view('utilisateur.incidents.create', compact('categorie','types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'required|string',
            'categorie'   => 'required|string',
            'type'        => 'required|string',
        ]);

        $incident = Incident::create([
            'titre'          => $request->titre,
            'description'    => $request->description,
            'categorie'      => $request->categorie,
            'type'           => $request->type,
            'statut'         => Incident::STATUT_NOUVEAU,
            'utilisateur_id' => Auth::id(),
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => Auth::id(),
            'action'      => 'created',
            'details'     => 'Création de l’incident',
        ]);

        $request->user()->notify(new IncidentCreated($incident));

        return redirect()->route('utilisateur.incidents.show', $incident)->with('success', 'Incident créé avec succès.');
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== Incident::STATUT_NOUVEAU) {
            return redirect()->route('utilisateur.incidents.index')->with('error', 'Impossible de modifier un incident déjà traité.');
        }

        return view('utilisateur.incidents.edit', compact('incident'));
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        if ($incident->statut !== Incident::STATUT_NOUVEAU) {
            return redirect()->route('utilisateur.incidents.index')->with('error', 'Impossible de mettre à jour un incident déjà traité.');
        }

        $request->validate([
            'titre'       => 'required|string',
            'description' => 'required|string',
        ]);

        $avant = $incident->only(['titre','description']);

        $incident->update([
            'titre'       => $request->titre,
            'description' => $request->description,
        ]);

        $apres = $incident->only(['titre','description']);
        $modifs = [];
        foreach ($apres as $k => $v) {
            $old = $avant[$k] ?? null;
            if ($old !== $v) {
                $modifs[$k] = ['old' => (string) $old, 'new' => (string) $v];
            }
        }
        if (count($modifs)) {
            IncidentLog::create([
                'incident_id' => $incident->id,
                'user_id'     => Auth::id(),
                'action'      => 'updated',
                'meta'        => ['changes' => $modifs],
            ]);
        }

        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident mis à jour avec succès.');
    }

    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        if ($incident->statut !== Incident::STATUT_NOUVEAU) {
            return back()->with('error', 'Impossible de supprimer un incident déjà traité.');
        }

        $incident->delete();
        return redirect()->route('utilisateur.incidents.index')->with('success', 'Incident supprimé avec succès.');
    }

    public function show(Incident $incident)
    {
        $this->authorize('view', $incident);
        $incident->load(['utilisateur','gestionnaire','commentaires.auteur']);
        return view('utilisateur.incidents.show', compact('incident'));
    }

    public function confirmClose(Incident $incident)
    {
        $user = Auth::user();
        if ($incident->utilisateur_id !== $user->id) abort(403);
        if (Incident::normalizeStatus($incident->statut) !== Incident::STATUT_RESOLU) {
            return back()->with('error', 'Le ticket doit être en statut "résolu".');
        }

        $incident->setStatusWithLog(Incident::STATUT_FERME, 'Confirmation utilisateur');

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => $user->id,
            'action'      => 'closed_confirmed',
            'from_status' => Incident::STATUT_RESOLU,
            'to_status'   => Incident::STATUT_FERME,
            'details'     => 'Confirmation par l’utilisateur',
        ]);

        $label = Incident::labelForStatus(Incident::STATUT_FERME);
        return back()->with('success', 'Ticket passé à "'.$label.'".');
    }

    public function rejectClose(Incident $incident, Request $request)
    {
        $user = Auth::user();
        if ($incident->utilisateur_id !== $user->id) abort(403);

        $data = $request->validate(['reason' => 'required|string|min:5']);

        IncidentComment::create([
            'incident_id' => $incident->id,
            'user_id'     => $user->id,
            'commentaire' => $data['reason'],
        ]);

        $incident->setStatusWithLog(Incident::STATUT_EN_COURS, 'Réouverture suite à rejet utilisateur');

        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => $user->id,
            'action'      => 'close_rejected',
            'from_status' => Incident::STATUT_RESOLU,
            'to_status'   => Incident::STATUT_EN_COURS,
            'details'     => $data['reason'],
        ]);

        if ($incident->assignedUser) {
            $incident->assignedUser->notify(new IncidentReopenedByUser($incident, $data['reason']));
        }

        $label = Incident::labelForStatus(Incident::STATUT_EN_COURS);
        return back()->with('success', 'Réouvert. Le ticket revient à "'.$label.'".');
    }
}
