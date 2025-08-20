<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;
use App\Models\IncidentLog;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function incidentsCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        $rows = Incident::query()
            ->when($user && $user->role === 'utilisateur', fn($q) => $q->where('utilisateur_id', $user->id))
            ->when($user && $user->role === 'resolveur', fn($q) => $q)
            ->with(['assignedUser'])
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="incidents.csv"',
        ];

        return new StreamedResponse(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Code','Titre','Priorité','Statut','Assigné à','Niveau Escalade','Catégorie','Attribué à','SLA',
                'Créé','Résolu','Fermé'
            ]);

            foreach ($rows as $i) {
                fputcsv($out, [
                    $i->public_id ?? ('INC-'.str_pad($i->id, 4, '0', STR_PAD_LEFT)),
                    $i->titre,
                    ucfirst(strtolower($i->priority ?? $i->priorite ?? '')),
                    \App\Models\Incident::labelForStatus($i->statut),
                    optional($i->assignedUser)->name ?? 'Non assigné',
                    $i->escalation_level,
                    $i->categorie,
                    $i->attribue_a,
                    optional($i->sla_due_at)->format('Y-m-d H:i:s'),
                    optional($i->created_at)->format('Y-m-d H:i:s'),
                    optional($i->resolved_at)->format('Y-m-d H:i:s'),
                    optional($i->closed_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }

    public function incidentLogsCsv(Incident $incident): StreamedResponse
    {
        $user = Auth::user();
        if ($user->role === 'utilisateur' && (int) $incident->utilisateur_id !== (int) $user->id) {
            abort(403);
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="incident_'.$incident->id.'_historique.csv"',
        ];

        $logs = IncidentLog::where('incident_id', $incident->id)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return new StreamedResponse(function () use ($incident, $logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date','Utilisateur','Action','De','À','Détails']);

            foreach ($logs as $log) {
                fputcsv($out, [
                    optional($log->created_at)->format('Y-m-d H:i:s'),
                    $log->user?->name ?? '—',
                    $log->action,
                    $log->from_status ? \App\Models\Incident::labelForStatus($log->from_status) : '',
                    $log->to_status   ? \App\Models\Incident::labelForStatus($log->to_status)   : '',
                    $log->human_details,
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }
}
