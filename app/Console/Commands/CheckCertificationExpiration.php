<?php

namespace App\Console\Commands;

use App\Models\SocialNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Notifications\CertificationRenewalReminder;
use App\Notifications\CertificationExpiredNotification;
use Carbon\Carbon;

class CheckCertificationExpiration extends Command
{
    protected $signature = 'certification:check-expiration';
    protected $description = 'Check certified users for upcoming expiration and expired accounts.';

    public function handle(): int
    {
        $now = Carbon::now();

        $usersExpiringSoon = User::where('is_certified', true)
            ->whereNotNull('certified_until')
            ->whereBetween('certified_until', [$now->copy()->addDays(1), $now->copy()->addDays(3)])
            ->get();

        foreach ($usersExpiringSoon as $user) {
            SocialNotification::create([
                'user_id' => $user->id,
                'type' => 'certification_renewal_reminder',
                'data' => [
                    'expires_at' => $user->certified_until?->toDateTimeString(),
                    'message' => 'Votre certification expirera le ' . $user->certified_until?->format('d/m/Y') . '.',
                ],
            ]);

            $user->notify(new CertificationRenewalReminder($user->certified_until));
        }

        $usersExpired = User::where('is_certified', true)
            ->whereNotNull('certified_until')
            ->where('certified_until', '<', $now)
            ->get();

        foreach ($usersExpired as $user) {
            $user->forceFill(['is_certified' => false])->save();

            SocialNotification::create([
                'user_id' => $user->id,
                'type' => 'certification_expired',
                'data' => [
                    'message' => 'Votre certification a expiré. Renouvelez-la pour récupérer le badge.',
                ],
            ]);

            $user->notify(new CertificationExpiredNotification());
        }

        $this->info('Certification expiration check completed.');
        return 0;
    }
}
