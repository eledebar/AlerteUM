<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Incident;

class IncidentStatusChanged extends Notification
{
    use Queueable;
    public function __construct(public Incident $incident) {}
    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Mise à jour du statut de votre incident')
            ->greeting('Bonjour')
            ->line('Le statut de votre incident a changé: '.$this->incident->statut)
            ->action('Voir le ticket', url('/utilisateur/incidents/'.$this->incident->id))
            ->line('Merci.');
    }
}
