<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAjaxActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_like_returns_json_for_ajax_request(): void
    {
        $author = User::create([
            'name' => 'Auteur',
            'prenom' => 'Auteur',
            'nom' => 'Test',
            'matricule' => 'A001',
            'email' => 'auteur@example.com',
            'universite' => 'USN',
            'password' => bcrypt('password'),
        ]);
        $viewer = User::create([
            'name' => 'Viewer',
            'prenom' => 'Viewer',
            'nom' => 'Test',
            'matricule' => 'V001',
            'email' => 'viewer@example.com',
            'universite' => 'USN',
            'password' => bcrypt('password'),
        ]);
        $post = Post::create([
            'user_id' => $author->id,
            'contenu' => 'Bonjour',
            'likes_count' => 0,
            'comments_count' => 0,
            'shares_count' => 0,
        ]);

        $this->actingAs($viewer);

        $response = $this->postJson(route('posts.like', $post->id), [], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('liked', true);
    }
}
