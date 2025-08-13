<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Incident;
use App\Notifications\SlaBreached;
use Illuminate\Support\Facades\Notification;

class CheckSlaDeadlines extends Command
{
    protected $signature = 'itil:check-sla';
    protected $description = 'Notifier les dépassements de SLA sur incidents ouverts';

    public function handle()
    {
        $now = now();
        $affected = 0;

        $incidents = Incident::whereNull('closed_at')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', $now)
            ->get();

        foreach ($incidents as $incident) {
            $emails = array_filter(array_map('trim', explode(',', config('itil.admin_emails'))));
            if (config('itil.notify_admin_on_sla_breach') && $emails) {
                foreach ($emails as $email) {
                    Notification::route('mail', $email)->notify(new SlaBreached($incident));
                }
            }
            $affected++;
        }

        $this->info("SLA check terminé. Incidents affectés: {$affected}.");
        return Command::SUCCESS;
    }
}
