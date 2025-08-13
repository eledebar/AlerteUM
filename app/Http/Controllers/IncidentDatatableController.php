<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;

class IncidentDatatableController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (method_exists($user, 'estResolveur') && $user->estResolveur()) {
            $q = Incident::query();
            if ($request->query('assigned') === 'me') {
                $q->where('attribue_a', $user->id);
            }
        } elseif (method_exists($user, 'estUtilisateur') && $user->estUtilisateur()) {
            $q = $user->incidents(); 
        } else {
            $q = Incident::query();
        }

        if ($s = $request->query('statut')) {
            $q->where('statut', $s);
        }
        if ($p = $request->query('priority')) {
            $q->where('priority', $p);
        }
        if ($t = $request->query('text')) {
            $q->where(function ($qq) use ($t) {
                $qq->where('titre', 'like', '%'.$t.'%')
                   ->orWhere('public_id', 'like', '%'.$t.'%');
            });
        }
        $from = $request->query('from');
        $to   = $request->query('to');
        if ($from && $to) {
            $q->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59']);
        } elseif ($from) {
            $q->where('created_at', '>=', $from.' 00:00:00');
        } elseif ($to) {
            $q->where('created_at', '<=', $to.' 23:59:59');
        }

        $allowedSorts = [
            'created_at','priority','statut','escalation_level','sla_due_at','public_id','titre'
        ];
        $sort = in_array($request->query('sort'), $allowedSorts, true) ? $request->query('sort') : 'created_at';
        $dir  = $request->query('dir') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sort, $dir);

        $q->with(['utilisateur','gestionnaire']);

        $incidents = $q->paginate(10)->withQueryString();

        $data = $incidents->getCollection()->map(function (Incident $i) {
            return [
                'id'               => $i->id,
                'public_id'        => $i->public_id,
                'titre'            => $i->titre,
                'statut'           => $i->statut,
                'priority'         => $i->priority,
                'escalation_level' => (int) ($i->escalation_level ?? 0),
                'sla_due_at'       => optional($i->sla_due_at)->format('Y-m-d H:i'),
                'assigned_to'      => optional($i->gestionnaire)->name,   
                'created_by'       => optional($i->utilisateur)->name,   
                'created_at'       => optional($i->created_at)->format('Y-m-d'),

                'urls' => [
                    'show_resolveur'     => route('resolveur.incidents.show', $i),
                    'priority_resolveur' => route('resolveur.incidents.priority', $i),
                    'escalate_resolveur' => route('resolveur.incidents.escalate', $i),
                    'show_admin'         => route('admin.incidents.show', $i),
                ],
            ];
        });

        return response()->json([
            'data' => $data->values(),
            'meta' => [
                'current_page' => $incidents->currentPage(),
                'last_page'    => $incidents->lastPage(),
                'total'        => $incidents->total(),
            ],
        ]);
    }
}
