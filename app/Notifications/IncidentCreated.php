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

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $i = $this->incident;
        $code = $i->public_id ?: ('INC-'.str_pad((string)$i->id, 4, '0', STR_PAD_LEFT));
        $url = url('/utilisateur/incidents/'.$i->id);

        return (new MailMessage)
            ->subject('Votre incident a été créé')
            ->greeting('Bonjour')
            ->line('Votre incident a été enregistré avec succès.')
            ->line('ID public: '.$code)
            ->action('Voir le ticket', $url)
            ->line('Merci.');
    }
}
