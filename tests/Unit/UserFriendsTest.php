<?php

namespace Tests\Unit;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFriendsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_friends_relation_returns_both_sent_and_received_accepted_requests(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'prenom' => 'Test',
            'nom' => 'User',
            'email' => 'test@example.com',
            'matricule' => 'TEST001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'test-user',
        ]);

        $friendA = User::create([
            'name' => 'Friend A',
            'prenom' => 'Friend',
            'nom' => 'A',
            'email' => 'friend.a@example.com',
            'matricule' => 'FRI001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'friend-a',
        ]);

        $friendB = User::create([
            'name' => 'Friend B',
            'prenom' => 'Friend',
            'nom' => 'B',
            'email' => 'friend.b@example.com',
            'matricule' => 'FRI002',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'friend-b',
        ]);

        FriendRequest::create([
            'sender_id' => $user->id,
            'receiver_id' => $friendA->id,
            'status' => 'accepted',
        ]);

        FriendRequest::create([
            'sender_id' => $friendB->id,
            'receiver_id' => $user->id,
            'status' => 'accepted',
        ]);

        $friendIds = $user->friends()->pluck('id')->all();

        $this->assertContains($friendA->id, $friendIds);
        $this->assertContains($friendB->id, $friendIds);
        $this->assertCount(2, $friendIds);
    }
}
