<?php

namespace Tests\Feature;

use App\Models\Evenement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvenementEngagementAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_liking_an_event_via_ajax_returns_json_payload(): void
    {
        $user = User::create([
            'name' => 'User One',
            'prenom' => 'User',
            'nom' => 'One',
            'matricule' => 'E001',
            'universite' => 'USN',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'handle' => 'user1',
        ]);
        $evenement = Evenement::create([
            'titre' => 'Hackathon',
            'description' => 'Hackathon test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay()->addHour(),
            'organisateur_id' => $user->id,
            'organisateur_type' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('evenements.like', $evenement));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('liked', true)
            ->assertJsonPath('likesCount', 1);
    }

    public function test_commenting_an_event_via_ajax_returns_json_payload(): void
    {
        $user = User::create([
            'name' => 'User Two',
            'prenom' => 'User',
            'nom' => 'Two',
            'matricule' => 'E002',
            'universite' => 'USN',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'handle' => 'user2',
        ]);
        $evenement = Evenement::create([
            'titre' => 'Rencontre',
            'description' => 'Rencontre test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay()->addHour(),
            'organisateur_id' => $user->id,
            'organisateur_type' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('evenements.comment', $evenement), ['contenu' => 'Très bien']);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('commentsCount', 1);
    }

    public function test_sharing_an_event_via_ajax_returns_json_payload(): void
    {
        $user = User::create([
            'name' => 'User Three',
            'prenom' => 'User',
            'nom' => 'Three',
            'matricule' => 'E003',
            'universite' => 'USN',
            'email' => 'user3@example.com',
            'password' => bcrypt('password'),
            'handle' => 'user3',
        ]);
        $evenement = Evenement::create([
            'titre' => 'Atelier',
            'description' => 'Atelier test',
            'date_debut' => now()->addDay(),
            'date_fin' => now()->addDay()->addHour(),
            'organisateur_id' => $user->id,
            'organisateur_type' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('evenements.share', $evenement));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('sharesCount', 1);
    }
}
