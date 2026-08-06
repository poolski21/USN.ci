<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class CertificationRenewalReminder extends Notification
{
    use Queueable;

    protected Carbon $expiresAt;

    public function __construct(Carbon $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre certification USN expire bientôt')
            ->greeting('Bonjour ' . $notifiable->prenom . ',')
            ->line('Votre compte certifié va expirer le ' . $this->expiresAt->format('d/m/Y') . '.')
            ->line('Pour conserver votre badge officiel, renouvelle votre certification avant cette date.')
            ->action('Renouveler ma certification', url('/certification'))
            ->line('Merci de faire confiance à USN.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'certification_renewal_reminder',
            'expires_at' => $this->expiresAt->toDateTimeString(),
            'message' => 'Votre certification expirera le ' . $this->expiresAt->format('d/m/Y') . '.',
        ];
    }
}
