<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_and_login_flow(): void
    {
        $response = $this->post(route('inscription.store'), [
            'prenom' => 'John',
            'nom' => 'Doe',
            'matricule' => 'JD123',
            'universite' => 'USN',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('profil.show'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
            'prenom' => 'John',
        ]);

        auth()->logout();

        $response = $this->post(route('connexion.store'), [
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('profil.show'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::create([
            'name' => 'Jane Doe',
            'prenom' => 'Jane',
            'nom' => 'Doe',
            'email' => 'jane.doe@example.com',
            'matricule' => 'JD124',
            'universite' => 'USN',
            'password' => bcrypt('password123'),
            'handle' => 'jane-doe',
        ]);

        $response = $this->from(route('connexion'))->post(route('connexion.store'), [
            'email' => 'jane.doe@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('connexion'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}
