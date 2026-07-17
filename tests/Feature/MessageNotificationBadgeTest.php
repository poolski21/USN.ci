<?php

namespace Tests\Feature;

use App\Models\SocialMessage;
use App\Models\SocialNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageNotificationBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_updates_endpoint_returns_unread_counts(): void
    {
        $user = User::create([
            'name' => 'Badge User',
            'prenom' => 'Badge',
            'nom' => 'User',
            'email' => 'badge@example.com',
            'matricule' => 'BAD001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'badge-user',
        ]);

        $sender = User::create([
            'name' => 'Sender',
            'prenom' => 'Sender',
            'nom' => 'User',
            'email' => 'sender-badge@example.com',
            'matricule' => 'SEN001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'sender-badge',
        ]);

        SocialMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'body' => 'Salut',
            'read_at' => null,
        ]);

        SocialNotification::create([
            'user_id' => $user->id,
            'type' => 'test_notification',
            'data' => json_encode(['message' => 'Nouvelle notification']),
            'read_at' => null,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('live.updates'));

        $response->assertOk()
            ->assertJsonStructure(['unreadMessages', 'unreadNotifications', 'pendingFriendRequests'])
            ->assertJson(['unreadMessages' => 1, 'unreadNotifications' => 1]);
    }
}
