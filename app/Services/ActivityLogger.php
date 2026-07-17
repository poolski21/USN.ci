<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;

class ActivityLogger
{
    public static function log(?User $user, string $action, string $description, array $metadata = []): ?UserActivity
    {
        if (! $user) {
            return null;
        }

        return UserActivity::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'metadata' => $metadata ?: null,
        ]);
    }
}
