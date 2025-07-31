<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NouvelleIncidentCree extends Notification
{
    use Queueable;

    protected $incident;

    public function __construct(Incident $incident)
    {
        // Aseguramos que el usuario esté cargado
        $incident->loadMissing('utilisateur');
        $this->incident = $incident;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Nouvelle incidence créée: « ' . $this->incident->titre . ' » par ' . ($this->incident->utilisateur->name ?? 'Utilisateur inconnu'),
            'url' => route('admin.incidents.show', $this->incident->id),
        ];
    }
}
