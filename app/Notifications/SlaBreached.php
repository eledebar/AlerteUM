<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Incident;

class SlaBreached extends Notification
{
    use Queueable;
    public function __construct(public Incident $incident) {}
    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('SLA en dépassement')
            ->greeting('Bonjour')
            ->line('Un incident a dépassé son SLA.')
            ->line('ID public: '.$this->incident->public_id.' (priority: '.$this->incident->priority.')')
            ->action('Voir le ticket', url('/admin/incidents/'.$this->incident->id));
    }
}
