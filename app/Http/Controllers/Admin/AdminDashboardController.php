<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $resolveurId = $request->integer('resolveur_id');
        $from = $this->parseDate($request->input('from'));
        $to = $this->parseDate($request->input('to'), true);

        $colRes = $this->colonneResolveur();

        $q = Incident::query();

        if ($resolveurId && $colRes) {
            $q->where($colRes, $resolveurId);
        }
        if ($from) {
            $q->where('created_at', '>=', $from);
        }
        if ($to) {
            $q->where('created_at', '<=', $to);
        }

        $base = clone $q;

        $nouveaux = (clone $base)->where('statut', 'nouveau')->count();
        $enCours = (clone $base)->whereIn('statut', ['en_cours', 'en cours'])->count();
        $resolus = (clone $base)->whereIn('statut', ['resolu', 'résolu'])->count();
        $fermes = (clone $base)->whereIn('statut', ['ferme', 'fermé'])->count();

        $parCategorie = $this->groupByCategorie(clone $base);

        $evolution = (clone $base)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $resolveurs = User::where('role', 'resolveur')->orderBy('name')->get(['id','name']);

        return view('admin.dashboard', [
            'resolveurs' => $resolveurs,
            'resolveurId' => $resolveurId,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'nouveaux' => $nouveaux,
            'enCours' => $enCours,
            'resolus' => $resolus,
            'fermes' => $fermes,
            'parCategorie' => $parCategorie,
            'evolution' => $evolution,
        ]);
    }

    protected function parseDate($v, $endOfDay = false)
    {
        if (!$v) return null;
        try {
            $d = Carbon::createFromFormat('Y-m-d', $v);
        } catch (\Throwable $e) {
            try {
                $d = Carbon::createFromFormat('d/m/Y', $v);
            } catch (\Throwable $e2) {
                return null;
            }
        }
        return $endOfDay ? $d->copy()->endOfDay() : $d->startOfDay();
    }

    protected function groupByCategorie($q)
    {
        if (Schema::hasColumn('incidents', 'categorie')) {
            return $q->selectRaw('COALESCE(categorie, "Sans catégorie") as categorie, COUNT(*) as total')
                     ->groupBy('categorie')->orderBy('categorie')->get();
        }
        if (Schema::hasColumn('incidents', 'type')) {
            return $q->selectRaw('COALESCE(type, "Sans catégorie") as categorie, COUNT(*) as total')
                     ->groupBy('type')->orderBy('type')->get();
        }
        return collect();
    }

    protected function colonneResolveur(): ?string
    {
        $cols = Schema::getColumnListing('incidents');
        $candidats = ['attribue_a','resolveur_id','resolver_id','assigned_to','assign_to'];
        foreach ($candidats as $c) {
            if (in_array($c, $cols, true)) return $c;
        }
        return null;
    }
}
