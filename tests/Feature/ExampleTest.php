<?php

namespace Tests\Feature;

use App\Models\FriendRequest;
use App\Models\Post;
use App\Models\SocialNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_profile_shows_friends_from_both_accepted_directions(): void
    {
        $user = User::create([
            'name' => 'Moustapha Diop',
            'prenom' => 'Moustapha',
            'nom' => 'Diop',
            'email' => 'moustapha@example.com',
            'matricule' => 'MST001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'moustapha-diop',
        ]);

        $friendOne = User::create([
            'name' => 'Ami Un',
            'prenom' => 'Ami',
            'nom' => 'Un',
            'email' => 'ami1@example.com',
            'matricule' => 'AMI001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'ami-un',
        ]);

        $friendTwo = User::create([
            'name' => 'Ami Deux',
            'prenom' => 'Ami',
            'nom' => 'Deux',
            'email' => 'ami2@example.com',
            'matricule' => 'AMI002',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'ami-deux',
        ]);

        FriendRequest::create([
            'sender_id' => $user->id,
            'receiver_id' => $friendOne->id,
            'status' => 'accepted',
        ]);

        FriendRequest::create([
            'sender_id' => $friendTwo->id,
            'receiver_id' => $user->id,
            'status' => 'accepted',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profil.show', $user->handle));

        $response->assertOk();
        $response->assertSee($friendOne->prenom);
        $response->assertSee($friendTwo->prenom);
    }

    public function test_profile_shows_pending_friend_requests_for_the_owner(): void
    {
        $user = User::create([
            'name' => 'Moustapha Diop',
            'prenom' => 'Moustapha',
            'nom' => 'Diop',
            'email' => 'moustapha2@example.com',
            'matricule' => 'MST002',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'moustapha-diop-2',
        ]);

        $requester = User::create([
            'name' => 'Requester User',
            'prenom' => 'Requester',
            'nom' => 'User',
            'email' => 'requester@example.com',
            'matricule' => 'REQ001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'requester-user',
        ]);

        FriendRequest::create([
            'sender_id' => $requester->id,
            'receiver_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profil.show', $user->handle));

        $response->assertOk();
        $response->assertSee('Demandes d’amis');
        $response->assertSee($requester->prenom);
    }

    public function test_profile_shows_friend_suggestions_for_the_owner(): void
    {
        $user = User::create([
            'name' => 'Moustapha Diop',
            'prenom' => 'Moustapha',
            'nom' => 'Diop',
            'email' => 'moustapha3@example.com',
            'matricule' => 'MST003',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'moustapha-diop-3',
        ]);

        $suggested = User::create([
            'name' => 'Suggested User',
            'prenom' => 'Suggested',
            'nom' => 'User',
            'email' => 'suggested@example.com',
            'matricule' => 'SUG001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'suggested-user',
        ]);

        $this->actingAs($user);

        $response = $this->get(route('profil.show', $user->handle));

        $response->assertOk();
        $response->assertSee('Suggestions d’amis');
        $response->assertSee('Nouveaux amis à découvrir');
        $response->assertSee($suggested->prenom);
    }

    public function test_liking_a_post_notifies_the_author(): void
    {
        $author = User::create([
            'name' => 'Author User',
            'prenom' => 'Author',
            'nom' => 'User',
            'email' => 'author@example.com',
            'matricule' => 'AUT001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'author-user',
        ]);

        $actor = User::create([
            'name' => 'Actor User',
            'prenom' => 'Actor',
            'nom' => 'User',
            'email' => 'actor@example.com',
            'matricule' => 'ACT001',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'actor-user',
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'contenu' => 'Publication de test',
            'visibilite' => 'public',
        ]);

        $this->actingAs($actor);

        $response = $this->post(route('posts.like', $post->id));

        $response->assertRedirect();
        $this->assertSame(1, $post->fresh()->likes_count);
        $this->assertDatabaseHas('social_notifications', [
            'user_id' => $author->id,
            'type' => 'post_liked',
        ]);
    }

    public function test_commenting_a_post_notifies_the_author(): void
    {
        $author = User::create([
            'name' => 'Author User 2',
            'prenom' => 'Author',
            'nom' => 'User',
            'email' => 'author2@example.com',
            'matricule' => 'AUT002',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'author-user-2',
        ]);

        $actor = User::create([
            'name' => 'Actor User 2',
            'prenom' => 'Actor',
            'nom' => 'User',
            'email' => 'actor2@example.com',
            'matricule' => 'ACT002',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'actor-user-2',
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'contenu' => 'Publication de test',
            'visibilite' => 'public',
        ]);

        $this->actingAs($actor);

        $response = $this->post(route('posts.comment', $post->id), [
            'contenu' => 'Super publication',
        ]);

        $response->assertRedirect();
        $this->assertSame(1, $post->fresh()->comments_count);
        $this->assertDatabaseHas('social_notifications', [
            'user_id' => $author->id,
            'type' => 'post_commented',
        ]);
    }

    public function test_sharing_a_post_notifies_the_author(): void
    {
        $author = User::create([
            'name' => 'Author User 3',
            'prenom' => 'Author',
            'nom' => 'User',
            'email' => 'author3@example.com',
            'matricule' => 'AUT003',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'author-user-3',
        ]);

        $actor = User::create([
            'name' => 'Actor User 3',
            'prenom' => 'Actor',
            'nom' => 'User',
            'email' => 'actor3@example.com',
            'matricule' => 'ACT003',
            'universite' => 'USN',
            'password' => bcrypt('password'),
            'handle' => 'actor-user-3',
        ]);

        $post = Post::create([
            'user_id' => $author->id,
            'contenu' => 'Publication de test',
            'visibilite' => 'public',
        ]);

        $this->actingAs($actor);

        $response = $this->post(route('posts.share', $post->id));

        $response->assertRedirect();
        $this->assertSame(1, $post->fresh()->shares_count);
        $this->assertDatabaseHas('social_notifications', [
            'user_id' => $author->id,
            'type' => 'post_shared',
        ]);
    }
}
