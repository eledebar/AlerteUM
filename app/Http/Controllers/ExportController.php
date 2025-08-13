<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Incident;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function incidentsCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        $rows = Incident::query()
            ->filter($request, $user)
            ->sorted($request)
            ->with('assignedUser')
            ->get();

        $filename = 'incidents_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'public_id','titre','statut','priority','escalation_level',
                'categorie','assigned_to','sla_due_at','created_at','resolved_at','closed_at'
            ]);

            foreach ($rows as $i) {
                fputcsv($out, [
                    $i->public_id,
                    $i->titre,
                    $i->statut,
                    $i->priority,
                    $i->escalation_level,
                    $i->categorie,
                    optional($i->assignedUser)->name ?? 'Non assigné',
                    optional($i->sla_due_at)->format('Y-m-d H:i:s'),
                    optional($i->created_at)->format('Y-m-d H:i:s'),
                    optional($i->resolved_at)->format('Y-m-d H:i:s'),
                    optional($i->closed_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }
}
