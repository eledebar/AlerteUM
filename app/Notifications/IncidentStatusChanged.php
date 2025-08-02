<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class IncidentStatusChanged extends Notification
{
    use Queueable;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("État de l'incident mis à jour")
                    ->line("L'état de votre incident '{$this->incident->titre}' a été mis à jour à : {$this->incident->statut}")
                    ->action('Voir l’incident', route('admin.incidents.show', $this->incident))
                    ->line('Merci de votre patience.');
    }

    public function toArray($notifiable)
    {
        return [
            'incident_id' => $this->incident->id,
            'titre' => $this->incident->titre,
            'statut' => $this->incident->statut,
        ];
    }
}
