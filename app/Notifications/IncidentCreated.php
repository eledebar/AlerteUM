<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Incident;

class IncidentCreated extends Notification
{
    use Queueable;
    public function __construct(public Incident $incident) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre incident a été créé')
            ->greeting('Bonjour')
            ->line('Votre incident a été enregistré avec succès.')
            ->line('ID public: '.$this->incident->public_id)
            ->action('Voir le ticket', url('/utilisateur/incidents/'.$this->incident->id))
            ->line('Merci.');
    }
}
