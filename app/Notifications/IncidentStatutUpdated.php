<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

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
        return ['database', 'mail']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Le statut de votre incident "' . $this->incident->titre . '" a été mis à jour à: ' . $this->incident->statut,
            'url' => route('utilisateur.incidents.show', $this->incident->id),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Statut de votre incident mis à jour')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Le statut de votre incident "' . $this->incident->titre . '" a été changé.')
            ->line('Nouveau statut: ' . $this->incident->statut)
            ->action('Voir l\'incident', route('utilisateur.incidents.show', $this->incident->id))
            ->line('Merci de votre patience.');
    }
}
