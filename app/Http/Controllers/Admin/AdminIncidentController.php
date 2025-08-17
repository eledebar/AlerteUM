<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Incident;

class AdminIncidentController extends Controller
{
    public function index(Request $requete)
    {
        $utilisateur = Auth::user();

        $parPage = (int) $requete->integer('per_page', 10);
        if ($parPage < 5) $parPage = 5;
        if ($parPage > 100) $parPage = 100;

        $q = Incident::query();

        $statut = $requete->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($requete->query('priority', $requete->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->whereRaw('lower(priority) = ?', [$prio]);
                if (Schema::hasColumn('incidents', 'priorite')) {
                    $qq->orWhereRaw('lower(priorite) = ?', [$prio]);
                }
            });
        }

        $de = $requete->query('from');
        $a  = $requete->query('to');
        if ($de && $a) {
            $q->whereBetween('created_at', [$de.' 00:00:00', $a.' 23:59:59']);
        } elseif ($de) {
            $q->where('created_at', '>=', $de.' 00:00:00');
        } elseif ($a) {
            $q->where('created_at', '<=', $a.' 23:59:59');
        }

        $terme = trim((string) $requete->query('q', ''));
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
        $triDemande = $requete->query('sort');
        if ($triDemande === 'priorite' && !Schema::hasColumn('incidents', 'priorite')) {
            $triDemande = 'priority';
        }
        $tri = in_array($triDemande, $trisAutorises, true) ? $triDemande : 'created_at';
        $dir = $requete->query('dir') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($tri, $dir);

        $q->with(['utilisateur','assignedUser']);

        $incidents = $q->paginate($parPage)->withQueryString();

        return view('admin.incidents.index', compact('incidents'));
    }

    public function show(Incident $incident)
    {
        $incident->load(['assignedUser','utilisateur','commentaires.auteur','logs']);
        return view('admin.incidents.show', compact('incident'));
    }

    public function assign(Request $requete, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }

    public function setStatus(Request $requete, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }

    public function escalate(Request $requete, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }
}
