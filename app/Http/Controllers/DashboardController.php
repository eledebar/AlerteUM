<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Incident;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->estAdmin()) {
            $total = Incident::count();
            $pendientes = Incident::where('statut', 'nouveau')->count();
            $resueltas = Incident::where('statut', 'résolu')->count();
            $enProceso = Incident::where('statut', 'en_cours')->count();
            $porTipo = Incident::select('titre', DB::raw('count(*) as total'))
                ->groupBy('titre')
                ->get();
        } elseif ($user->estUtilisateur()) {
            $total = $user->incidents()->count();
            $pendientes = $user->incidents()->where('statut', 'nouveau')->count();
            $resueltas = $user->incidents()->where('statut', 'résolu')->count();
            $enProceso = $user->incidents()->where('statut', 'en_cours')->count();
            $porTipo = $user->incidents()
                ->select('titre', DB::raw('count(*) as total'))
                ->groupBy('titre')
                ->get();
        } else {
            abort(403, 'Rol non autorisé.');
        }

        return view('dashboard', compact(
            'total', 'pendientes', 'resueltas', 'enProceso', 'porTipo'
        ));
    }
}
