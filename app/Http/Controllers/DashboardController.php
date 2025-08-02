<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\Incident;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $filtreAssigne = $request->query('assigned') === 'me';
        $dateFrom = $request->query('from');
        $dateTo = $request->query('to');

        $isValidDateRange = $dateFrom && $dateTo && strtotime($dateFrom) <= strtotime($dateTo);

        if ($user->estAdmin()) {
            $query = Incident::query();

            if ($filtreAssigne) {
                $query->where('attribue_a', $user->id);
            }

            if ($isValidDateRange) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }

        } elseif ($user->estUtilisateur()) {
            $query = $user->incidents();

            if ($isValidDateRange) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }
        } else {
            abort(403, 'Rôle non autorisé.');
        }

        $nouveaux = (clone $query)->where('statut', 'nouveau')->count();
        $enCours  = (clone $query)->where('statut', 'en_cours')->count();
        $resolus  = (clone $query)->where('statut', 'résolu')->count();
        $total    = $nouveaux + $enCours + $resolus;

        $parCategorie = (clone $query)
            ->select('categorie', DB::raw('count(*) as total'))
            ->groupBy('categorie')
            ->orderBy('total', 'desc')
            ->get();

        $evolution = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $parUtilisateur = $user->estAdmin() ? (clone $query)
            ->select('attribue_a', DB::raw('count(*) as total'))
            ->whereNotNull('attribue_a')
            ->groupBy('attribue_a')
            ->get()
            ->map(function ($item) {
                $assignedUser = User::find($item->attribue_a);
                return [
                    'name' => $assignedUser?->name ?? 'Non assigné',
                    'total' => $item->total,
                ];
            }) : collect();

        $incidents = (clone $query)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard', compact(
            'nouveaux', 'enCours', 'resolus', 'total',
            'parCategorie', 'evolution', 'parUtilisateur', 'incidents'
        ));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $query = $user->estAdmin() ? Incident::query() : $user->incidents();

        if ($request->query('assigned') === 'me') {
            $query->where('attribue_a', $user->id);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->query('from'), $request->query('to')]);
        }

        $incidents = $query->orderBy('created_at', 'desc')->get();

        $csv = "ID,Date,Statut,Catégorie,Assigné à\n";
        foreach ($incidents as $incident) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s\n",
                $incident->id,
                $incident->created_at->format('Y-m-d'),
                $incident->statut,
                $incident->categorie,
                $incident->attribue_a ? User::find($incident->attribue_a)?->name ?? 'Non assigné' : 'Non assigné'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="incidents.csv"',
        ]);
    }
}
