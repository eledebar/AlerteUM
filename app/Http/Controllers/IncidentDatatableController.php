<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;

class IncidentDatatableController extends Controller
{
    public function index(Request $requete)
    {
        $utilisateur = Auth::user();

        $q = Incident::query();

        $statut = $requete->query('statut');
        if ($statut !== null && $statut !== '') {
            $q->where('statut', $statut);
        }

        $prio = strtolower($requete->query('priority', $requete->query('priorite', '')));
        if ($prio !== '') {
            $q->where(function ($qq) use ($prio) {
                $qq->where('priority', $prio)->orWhere('priorite', $prio);
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
        $tri = in_array($requete->query('sort'), $trisAutorises, true) ? $requete->query('sort') : 'created_at';
        $dir = $requete->query('dir') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($tri, $dir);

        $q->with(['utilisateur','assignedUser']);

        $parPage = (int) $requete->integer('per_page', 10);
        if ($parPage < 5) $parPage = 5;
        if ($parPage > 100) $parPage = 100;

        $incidents = $q->paginate($parPage)->withQueryString();

        $data = $incidents->getCollection()->map(function ($i) {
            $code = $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT));
            $prio = strtolower($i->priority ?? $i->priorite ?? '');
            $sla  = $i->sla_due_at ? (now()->lte($i->sla_due_at) ? 'OK' : 'Breach') : '—';
            return [
                'id' => $i->id,
                'code' => $code,
                'titre' => $i->titre,
                'priorite' => $prio,
                'statut' => $i->statut,
                'assigné' => optional($i->assignedUser)->name,
                'demandeur' => optional($i->utilisateur)->name,
                'sla' => $sla,
                'créé' => optional($i->created_at)->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'data' => $data->values(),
            'meta' => [
                'current_page' => $incidents->currentPage(),
                'last_page' => $incidents->lastPage(),
                'total' => $incidents->total(),
            ],
        ]);
    }
}
