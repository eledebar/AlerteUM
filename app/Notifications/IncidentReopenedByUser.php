<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class IncidentReopenedByUser extends Notification
{
    use Queueable;

    public function __construct(public Incident $incident, public string $reason)
    {
        $this->incident->loadMissing('utilisateur');
    }

    
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

  
    public function toMail($notifiable)
    {
        $publicId = $this->incident->public_id ?: ('INC-' . str_pad((string) $this->incident->id, 4, '0', STR_PAD_LEFT));
        $titre    = $this->incident->titre ?? '';
        $name     = $this->incident->utilisateur->name ?? "L’utilisateur";

        return (new MailMessage)
            ->subject('Incident réouvert par l’utilisateur')
            ->greeting('Bonjour')
            ->line("{$name} a réouvert l’incident {$publicId}" . ($titre ? " ({$titre})." : '.'))
            ->line("Motif : {$this->reason}")
            ->action('Voir l’incident', route('resolveur.incidents.show', $this->incident));
    }

  
    public function toArray($notifiable): array
    {
        $publicId = $this->incident->public_id ?: ('INC-' . str_pad((string) $this->incident->id, 4, '0', STR_PAD_LEFT));
        $titre    = $this->incident->titre ?? '';
        $name     = $this->incident->utilisateur->name ?? "L’utilisateur";

        return [
            'incident_id' => $this->incident->id,
            'public_id'   => $this->incident->public_id,
            'titre'       => $this->incident->titre,
            'reason'      => $this->reason,
            'url'         => route('resolveur.incidents.show', $this->incident),

            'message'     => "{$name} a réouvert l’incident {$publicId}" . ($titre ? " ({$titre})" : ''),
            'user_name'   => $name,
            'action'      => 'reopened',
        ];
    }
}
