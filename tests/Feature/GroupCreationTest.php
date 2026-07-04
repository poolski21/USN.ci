<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_group_with_max_members_limit(): void
    {
        $user = User::create([
            'name' => 'Alice Dupont',
            'prenom' => 'Alice',
            'nom' => 'Dupont',
            'matricule' => 'ABC123',
            'handle' => 'alice-dupont',
            'email' => 'alice@example.com',
            'password' => bcrypt('password123'),
            'universite' => 'USN',
        ]);

        $response = $this->actingAs($user)->post(route('groupes.store'), [
            'nom' => 'Groupe Dev',
            'description' => 'Discussion technique',
            'visibilite' => 'public',
            'max_members' => 25,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('groups', [
            'nom' => 'Groupe Dev',
            'max_members' => 25,
            'admin_id' => $user->id,
        ]);
    }

    public function test_non_friend_only_sees_public_posts_on_other_profile(): void
    {
        $owner = User::create([
            'name' => 'Bob Martin',
            'prenom' => 'Bob',
            'nom' => 'Martin',
            'matricule' => 'XYZ789',
            'handle' => 'bob-martin',
            'email' => 'bob@example.com',
            'password' => bcrypt('password123'),
            'universite' => 'USN',
        ]);

        $viewer = User::create([
            'name' => 'Cara Lee',
            'prenom' => 'Cara',
            'nom' => 'Lee',
            'matricule' => 'LMN456',
            'handle' => 'cara-lee',
            'email' => 'cara@example.com',
            'password' => bcrypt('password123'),
            'universite' => 'USN',
        ]);

        Post::create([
            'user_id' => $owner->id,
            'contenu' => 'Publication publique',
            'visibilite' => 'public',
        ]);

        Post::create([
            'user_id' => $owner->id,
            'contenu' => 'Publication privée',
            'visibilite' => 'prive',
        ]);

        $response = $this->actingAs($viewer)->get(route('profil.show', $owner->handle));

        $response->assertSee('Publication publique');
        $response->assertDontSee('Publication privée');
    }
}
