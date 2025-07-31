<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Incident;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $filtreAssigne = $request->query('assigned') === 'me';

        if ($user->estAdmin()) {
            $queryBase = Incident::query();

            if ($filtreAssigne) {
                $queryBase->where('attribue_a', $user->id);
            }

            // Conteos individuales por statut
            $pendientes = (clone $queryBase)->where('statut', 'nouveau')->count();
            $enProceso  = (clone $queryBase)->where('statut', 'en_cours')->count();
            $resueltas  = (clone $queryBase)->where('statut', 'résolu')->count();

            $porTipo = (clone $queryBase)
                ->select('titre', DB::raw('count(*) as total'))
                ->groupBy('titre')
                ->get();

            $total = $pendientes + $enProceso + $resueltas;

        } elseif ($user->estUtilisateur()) {
            $queryBase = $user->incidents();

            $pendientes = (clone $queryBase)->where('statut', 'nouveau')->count();
            $enProceso  = (clone $queryBase)->where('statut', 'en_cours')->count();
            $resueltas  = (clone $queryBase)->where('statut', 'résolu')->count();

            $porTipo = (clone $queryBase)
                ->select('titre', DB::raw('count(*) as total'))
                ->groupBy('titre')
                ->get();

            $total = $pendientes + $enProceso + $resueltas;

        } else {
            abort(403, 'Rôle non autorisé.');
        }

        return view('dashboard', [
            'pendientes' => $pendientes,
            'enProceso' => $enProceso,
            'resueltas' => $resueltas,
            'total' => $total,
            'porTipo' => $porTipo
        ]);
    }
}
