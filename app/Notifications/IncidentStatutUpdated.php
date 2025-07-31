<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class IncidentStatutUpdated extends Notification
{
    use Queueable;

    protected $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'message' => 'Le statut de votre incident "' . $this->incident->titre . '" a été mis à jour: ' . $this->incident->statut,
            'url' => route('utilisateur.incidents.show', $this->incident->id),
        ]);
    }
}
