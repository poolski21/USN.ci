<?php

namespace Tests\Feature;

use App\Models\CertificationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertifiedAccountFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_certified_account_and_admin_can_approve_it(): void
    {
        $user = User::create([
            'name' => 'Alice Example',
            'prenom' => 'Alice',
            'nom' => 'Example',
            'email' => 'alice@example.com',
            'matricule' => 'AL001',
            'universite' => 'USN',
            'password' => bcrypt('password123'),
            'handle' => 'alice-example',
        ]);

        $this->actingAs($user);
        $this->withoutMiddleware();

        $response = $this->post(route('certification.store'), [
            'university' => 'USN',
            'package' => 'standard',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('certification_requests', [
            'user_id' => $user->id,
            'university' => 'USN',
            'status' => 'pending',
        ]);
        $this->assertSame('pending', $user->fresh()->certification_status);

        $admin = User::create([
            'name' => 'Admin Example',
            'prenom' => 'Admin',
            'nom' => 'Example',
            'email' => 'admin@example.com',
            'matricule' => 'AD001',
            'universite' => 'USN',
            'password' => bcrypt('password123'),
            'handle' => 'admin-example',
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $request = CertificationRequest::latest()->first();

        $approveResponse = $this->post(route('admin.certifications.approve', $request));
        $approveResponse->assertRedirect(route('admin.certifications'));

        $this->assertDatabaseHas('certification_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);
        $this->assertTrue($user->fresh()->is_certified);
        $this->assertSame('USN', $user->fresh()->certified_university);
    }
}
