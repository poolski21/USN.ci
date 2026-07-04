<?php

namespace Tests\Feature;

use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupMemberSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_friend_as_group_member(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'prenom' => 'Admin',
            'nom' => 'User',
            'email' => 'admin@example.com',
            'matricule' => 'ADM001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'admin-user',
        ]);
        $friend = User::create([
            'name' => 'Friend User',
            'prenom' => 'Friend',
            'nom' => 'User',
            'email' => 'friend@example.com',
            'matricule' => 'FRI001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'friend-user',
        ]);

        FriendRequest::create([
            'sender_id' => $admin->id,
            'receiver_id' => $friend->id,
            'status' => 'accepted',
        ]);

        $group = Group::create([
            'nom' => 'Groupe test',
            'slug' => 'groupe-test',
            'admin_id' => $admin->id,
            'description' => 'Test group',
            'visibilite' => 'public',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('groupes.members.add', $group->slug), [
            'user_id' => $friend->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $friend->id,
        ]);
    }
}
