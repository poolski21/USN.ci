<?php

namespace Tests\Feature;

use App\Models\CertificationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CertifiedAccountFlowKadevPayTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_with_valid_signature_and_paid_status_certifies_user(): void
    {
        // fake verification endpoint
        Http::fake([
            'https://pay.kadev.ci/api/v1/transactions/verify/*' => Http::response(['status' => 'paid'], 200),
        ]);

        config(['services.kadevpay.webhook_secret' => 'test-webhook-secret']);

        $user = User::create([
            'name' => 'Jane',
            'prenom' => 'Jane',
            'nom' => 'Doe',
            'email' => 'jane@example.com',
            'matricule' => 'J001',
            'universite' => 'USN',
            'password' => bcrypt('pass'),
            'handle' => 'jane',
        ]);

        $request = CertificationRequest::create([
            'user_id' => $user->id,
            'reference' => 'KDV-TESTREF',
            'university' => 'USN',
            'package' => 'standard',
            'amount' => 25000,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        $payload = [
            'event' => 'payment.success',
            'data' => [
                'reference' => $request->reference,
                'status' => 'paid',
                'amount' => $request->amount,
                'currency' => 'XOF',
                'transaction_id' => 'KDVTX123',
            ],
        ];

        $raw = json_encode($payload);
        $signature = hash_hmac('sha512', $raw, config('services.kadevpay.webhook_secret'));

        $response = $this->postJson(route('webhooks.kadevpay'), $payload, ['X-KadevPay-Signature' => $signature]);
        $response->assertStatus(200);

        $this->assertTrue($user->fresh()->is_certified);
        $this->assertDatabaseHas('certification_requests', ['reference' => $request->reference, 'payment_status' => 'paid']);
    }

    public function test_webhook_with_invalid_signature_returns_401(): void
    {
        $payload = ['event' => 'payment.success', 'data' => ['reference' => 'KDV-UNKNOWN', 'status' => 'paid']];
        $response = $this->postJson(route('webhooks.kadevpay'), $payload, ['X-KadevPay-Signature' => 'bad']);
        $response->assertStatus(401);
    }

    public function test_duplicate_webhook_is_idempotent(): void
    {
        Http::fake(['https://pay.kadev.ci/api/v1/transactions/verify/*' => Http::response(['status' => 'paid'], 200)]);
        config(['services.kadevpay.webhook_secret' => 'test-webhook-secret']);

        $user = User::create(['name'=>'B','prenom'=>'B','nom'=>'B','email'=>'b@example.com','matricule'=>'B1','universite'=>'USN','password'=>bcrypt('p'),'handle'=>'b']);
        $request = CertificationRequest::create(['user_id'=>$user->id,'reference'=>'KDV-DUP','university'=>'USN','package'=>'standard','amount'=>25000,'payment_status'=>'pending','status'=>'pending']);

        $payload = ['event'=>'payment.success','data'=>['reference'=>$request->reference,'status'=>'paid','amount'=>$request->amount,'currency'=>'XOF','transaction_id'=>'TXDUP']];
        $raw = json_encode($payload);
        $signature = hash_hmac('sha512', $raw, config('services.kadevpay.webhook_secret'));

        $first = $this->postJson(route('webhooks.kadevpay'), $payload, ['X-KadevPay-Signature' => $signature]);
        $first->assertStatus(200);

        $firstCertifiedAt = $user->fresh()->certified_at;

        $second = $this->postJson(route('webhooks.kadevpay'), $payload, ['X-KadevPay-Signature' => $signature]);
        $second->assertStatus(200);

        $this->assertEquals($firstCertifiedAt->toDateTimeString(), $user->fresh()->certified_at->toDateTimeString());
    }

    public function test_webhook_with_rejected_status_returns_200_but_does_not_certify(): void
    {
        Http::fake(['https://pay.kadev.ci/api/v1/transactions/verify/*' => Http::response(['status' => 'failed'], 200)]);
        config(['services.kadevpay.webhook_secret' => 'test-webhook-secret']);

        $user = User::create(['name'=>'R','prenom'=>'R','nom'=>'R','email'=>'r@example.com','matricule'=>'R1','universite'=>'USN','password'=>bcrypt('p'),'handle'=>'r']);
        $request = CertificationRequest::create(['user_id'=>$user->id,'reference'=>'KDV-REJ','university'=>'USN','package'=>'standard','amount'=>25000,'payment_status'=>'pending','status'=>'pending']);

        $payload = ['event'=>'payment.success','data'=>['reference'=>$request->reference,'status'=>'paid','amount'=>$request->amount,'currency'=>'XOF','transaction_id'=>'TXREJ']];
        $raw = json_encode($payload);
        $signature = hash_hmac('sha512', $raw, config('services.kadevpay.webhook_secret'));

        $response = $this->postJson(route('webhooks.kadevpay'), $payload, ['X-KadevPay-Signature' => $signature]);
        $response->assertStatus(200);

        $this->assertFalse($user->fresh()->is_certified);
        $this->assertDatabaseHas('certification_requests', ['reference' => $request->reference, 'payment_status' => 'pending']);
    }
}
