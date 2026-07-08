<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePostVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_composer_offers_visibility_choices_for_posts(): void
    {
        $user = User::create([
            'name' => 'Alice',
            'prenom' => 'Alice',
            'nom' => 'Test',
            'matricule' => 'A100',
            'email' => 'alice@example.com',
            'universite' => 'USN',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profil.show', ['handle' => $user->id]));

        $response->assertOk();
        $response->assertSee('name="visibilite"', false);
        $response->assertSee('value="public"', false);
        $response->assertSee('value="amis"', false);
        $response->assertSee('value="prive"', false);
    }

    public function test_profile_shows_comments_for_the_post_author(): void
    {
        $author = User::create([
            'name' => 'Bob',
            'prenom' => 'Bob',
            'nom' => 'Test',
            'matricule' => 'B200',
            'email' => 'bob@example.com',
            'universite' => 'USN',
            'password' => bcrypt('password'),
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'contenu' => 'Publication test',
            'visibilite' => 'public',
            'likes_count' => 0,
            'comments_count' => 1,
            'shares_count' => 0,
        ]);

        PostComment::create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'contenu' => 'Commentaire visible par l’auteur',
        ]);

        $this->actingAs($author);

        $response = $this->get(route('profil.show', ['handle' => $author->id]));

        $response->assertOk();
        $response->assertSee('Commentaire visible par l’auteur');
    }
}
