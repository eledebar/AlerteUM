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

        $dateFrom = $request->input('date_debut', $request->input('from'));
        $dateTo   = $request->input('date_fin',   $request->input('to'));
        $fromTs = $dateFrom ? strtotime($dateFrom) : false;
        $toTs   = $dateTo   ? strtotime($dateTo)   : false;
        $isValidDateRange = ($fromTs !== false) && ($toTs !== false) && ($fromTs <= $toTs);

        if ($user->role === 'admin') {
            $query = Incident::query();
            if ($filtreAssigne) {
                $query->where('attribue_a', $user->id);
            }
        } elseif ($user->estResolveur()) {
            $query = Incident::query();
            if ($filtreAssigne) {
                $query->where('attribue_a', $user->id);
            }
        } elseif ($user->estUtilisateur()) {
            $query = $user->incidents();
        } else {
            abort(403, 'Rôle non autorisé.');
        }

        if ($isValidDateRange) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        $nouveaux = (clone $query)->where('statut', 'nouveau')->count();
        $enCours  = (clone $query)->where('statut', 'en_cours')->count();
        $resolus  = (clone $query)->where('statut', 'résolu')->count();
        $fermes   = (clone $query)->whereIn('statut', ['fermé', 'ferme'])->count(); 
        $total    = $nouveaux + $enCours + $resolus + $fermes;

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

        $mostrarPorUsuario = ($user->role === 'admin') || $user->estResolveur();

        $parUtilisateur = $mostrarPorUsuario ? (clone $query)
            ->select('attribue_a', DB::raw('count(*) as total'))
            ->whereNotNull('attribue_a')
            ->groupBy('attribue_a')
            ->get()
            ->map(function ($item) {
                $assignedUser = User::find($item->attribue_a);
                return [
                    'name'  => $assignedUser?->name ?? 'Non assigné',
                    'total' => $item->total,
                ];
            }) : collect();

        $incidents = (clone $query)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard', compact(
            'nouveaux', 'enCours', 'resolus', 'fermes', 'total',
            'parCategorie', 'evolution', 'parUtilisateur', 'incidents'
        ));
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin' || $user->estResolveur()) {
            $query = Incident::query();
            if ($request->query('assigned') === 'me') {
                $query->where('attribue_a', $user->id);
            }
        } else {
            $query = $user->incidents();
        }

        $from = $request->input('date_debut', $request->input('from'));
        $to   = $request->input('date_fin',   $request->input('to'));
        $fromTs = $from ? strtotime($from) : false;
        $toTs   = $to   ? strtotime($to)   : false;
        if (($fromTs !== false) && ($toTs !== false) && ($fromTs <= $toTs)) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->query('statut'));
        }

        $incidents = $query->orderBy('created_at', 'desc')->get();

        $csv = "ID,Date,Statut,Catégorie,Assigné à\n";
        foreach ($incidents as $incident) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s\n",
                $incident->id,
                optional($incident->created_at)->format('Y-m-d'),
                $incident->statut,
                $incident->categorie,
                $incident->attribue_a ? (User::find($incident->attribue_a)?->name ?? 'Non assigné') : 'Non assigné'
            );
        }

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=\"incidents.csv\"',
        ]);
    }
}
