<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total'   => Incident::count(),
            'nouveau' => Incident::where('statut', 'nouveau')->count(),
            'en_cours'=> Incident::where('statut', 'en_cours')->count(),
            'résolu'  => Incident::where('statut', 'résolu')->count(),
            'fermé'   => Incident::where('statut', 'fermé')->count(),
        ];
        $incidents = Incident::with('logs')->latest()->limit(20)->get();
        return view('admin.dashboard', compact('stats', 'incidents'));
    }
}
