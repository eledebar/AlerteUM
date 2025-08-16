<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;

class AdminIncidentController extends Controller
{
public function index(Request $request)
{
    $user = Auth::user();

    $perPage = (int) $request->integer('per_page', 10);
    $perPage = in_array($perPage, [10, 15, 25, 50]) ? $perPage : 10;

    $incidents = Incident::query()
        ->filter($request, $user)
        ->sorted($request)
        ->with(['assignedUser','utilisateur','lastLog'])   
        ->paginate($perPage)
        ->withQueryString();

    return view('admin.incidents.index', compact('incidents'));
}

    public function show(Incident $incident)
    {
        $incident->load(['assignedUser','utilisateur','commentaires.auteur','logs']);
        return view('admin.incidents.show', compact('incident'));
    }

    public function assign(Request $request, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }

    public function setStatus(Request $request, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }

    public function escalate(Request $request, Incident $incident)
    {
        return back()->with('error', 'Vue administrateur en lecture seule.');
    }
}
