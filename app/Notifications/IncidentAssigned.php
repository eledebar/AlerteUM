<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Incident;

class IncidentAssigned extends Notification
{
    use Queueable;
    public function __construct(public Incident $incident) {}
    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle assignation d\'incident')
            ->greeting('Bonjour')
            ->line('Un incident vous a été assigné.')
            ->line('ID public: '.$this->incident->public_id)
            ->action('Ouvrir le ticket', url('/resolveur/incidents/'.$this->incident->id))
            ->line('Merci.');
    }
}
