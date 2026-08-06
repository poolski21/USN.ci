<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificationExpiredNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre certification USN a expiré')
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Votre certification de compte a expiré et votre badge officiel a été retiré.')
            ->line('Vous pouvez renouveler la certification à tout moment depuis votre espace.')
            ->action('Renouveler ma certification', url('/certification'))
            ->line('Merci de rester actif sur USN.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'certification_expired',
            'message' => 'Votre certification a expiré. Renouvelez-la pour récupérer le badge.',
        ];
    }
}
