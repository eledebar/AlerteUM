<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;
use App\Models\User;
use App\Models\IncidentLog;

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
            ->with(['assignedUser'])
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.incidents.index', compact('incidents'));
    }

    public function show(Incident $incident)
    {
        $incident->load(['assignedUser', 'logs']);
        return view('admin.incidents.show', compact('incident'));
    }

  
    public function assign(Request $request, Incident $incident)
    {
        $request->validate([
            'attribue_a' => 'nullable|exists:users,id',
        ]);

        $oldUser = $incident->assignedUser;
        $incident->attribue_a = $request->attribue_a;

        if ($incident->attribue_a && ($incident->statut === (defined(Incident::class.'::STATUT_NOUVEAU') ? Incident::STATUT_NOUVEAU : 'nouveau'))) {
            $incident->statut = defined(Incident::class.'::STATUT_EN_COURS') ? Incident::STATUT_EN_COURS : 'en_cours';
        }

        $incident->save();

        $newUser = $incident->assignedUser;
        IncidentLog::create([
            'incident_id' => $incident->id,
            'user_id'     => Auth::id(),
            'action'      => 'assigned',
            'details'     => sprintf(
                'Assigné: %s → %s',
                $oldUser?->name ?? 'Non assigné',
                $newUser?->name ?? 'Non assigné'
            ),
        ]);

        return back()->with('success', 'Incident assigné.');
    }

    public function setStatus(Request $request, Incident $incident)
    {
        $request->validate([
            'statut' => 'required|string|in:nouveau,en_cours,résolu,fermé',
        ]);

        $incident->setStatusWithLog($request->statut);

        return back()->with('success', 'Statut mis à jour.');
    }
}
