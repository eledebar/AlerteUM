<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Incident;

class IncidentEscalated extends Notification
{
    use Queueable;
    public function __construct(public Incident $incident, public ?string $reason = null) {}
    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Incident escaladé')
            ->greeting('Bonjour')
            ->line('L\'incident a été escaladé (level: '.$this->incident->escalation_level.').');
        if ($this->reason) $mail->line('Reason: '.$this->reason);
        return $mail->action('Voir le ticket', url('/admin/incidents/'.$this->incident->id));
    }
}
