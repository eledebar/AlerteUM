<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NouvelleIncidentCree extends Notification
{
    use Queueable;

    public $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // ← Añadimos "mail"
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Nouvelle incidence créée: « ' . $this->incident->titre . ' » par ' . $this->incident->utilisateur->name,
            'url' => route('admin.incidents.show', $this->incident->id),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle incidence créée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Une nouvelle incidence a été créée:')
            ->line('Titre: ' . $this->incident->titre)
            ->action('Voir l\'incident', route('admin.incidents.show', $this->incident->id))
            ->line('Merci d\'utiliser notre application.');
    }
}