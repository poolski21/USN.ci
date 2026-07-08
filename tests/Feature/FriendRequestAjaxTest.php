<?php

namespace Tests\Feature;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendRequestAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_friend_request_via_ajax_returns_json_state(): void
    {
        $sender = User::create([
            'name' => 'Sender User',
            'prenom' => 'Sender',
            'nom' => 'User',
            'email' => 'sender@example.com',
            'password' => bcrypt('password'),
            'handle' => 'sender',
            'matricule' => 'M001',
            'universite' => 'USN',
        ]);
        $receiver = User::create([
            'name' => 'Receiver User',
            'prenom' => 'Receiver',
            'nom' => 'User',
            'email' => 'receiver@example.com',
            'password' => bcrypt('password'),
            'handle' => 'receiver',
            'matricule' => 'M002',
            'universite' => 'USN',
        ]);

        $response = $this->actingAs($sender)
            ->postJson(route('friend.requests.send', $receiver->handle));

        $response->assertOk()
            ->assertJsonPath('state', 'pending')
            ->assertJsonPath('message', 'Demande d\'ami envoyée.');

        $this->assertDatabaseHas('friend_requests', [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);
    }

    public function test_accepting_friend_request_via_ajax_returns_json_state(): void
    {
        $sender = User::create([
            'name' => 'Sender User',
            'prenom' => 'Sender',
            'nom' => 'User',
            'email' => 'sender2@example.com',
            'password' => bcrypt('password'),
            'handle' => 'sender2',
            'matricule' => 'M003',
            'universite' => 'USN',
        ]);
        $receiver = User::create([
            'name' => 'Receiver User',
            'prenom' => 'Receiver',
            'nom' => 'User',
            'email' => 'receiver2@example.com',
            'password' => bcrypt('password'),
            'handle' => 'receiver2',
            'matricule' => 'M004',
            'universite' => 'USN',
        ]);
        $request = FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($receiver)
            ->postJson(route('friend.requests.accept', $request->id));

        $response->assertOk()
            ->assertJsonPath('state', 'accepted');

        $this->assertDatabaseHas('friend_requests', [
            'id' => $request->id,
            'status' => 'accepted',
        ]);
    }
}
