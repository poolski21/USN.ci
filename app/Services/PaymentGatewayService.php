<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    public function generateReference(): string
    {
        return 'KDV-' . strtoupper(Str::random(10));
    }

    protected function secretKey(): string
    {
        return config('services.kadevpay.secret_key', '');
    }

    protected function webhookSecret(): string
    {
        return config('services.kadevpay.webhook_secret', '');
    }

    /**
     * Verify transaction by reference via Kadev Pay API.
     * Returns status string (e.g. 'paid', 'failed', 'unknown')
     */
    public function verifyTransaction(string $reference): string
    {
        try {
            $url = "https://pay.kadev.ci/api/v1/transactions/verify/" . urlencode($reference);
            $response = Http::withToken($this->secretKey())
                ->acceptJson()
                ->get($url);

            $body = $response->json();
            // Expect e.g. ['status' => 'paid', 'data' => [...]]
            $status = data_get($body, 'status') ?? data_get($body, 'data.status') ?? 'unknown';
            return (string) $status;
        } catch (\Throwable $e) {
            Log::warning('KadevPay verifyTransaction failed', ['reference' => $reference, 'error' => $e->getMessage()]);
            return 'unknown';
        }
    }

    /**
     * Verify webhook signature. $payload must be the raw request body string.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = $this->webhookSecret();
        if ($secret === '') {
            return false;
        }

        $computed = hash_hmac('sha512', $payload, $secret);
        return hash_equals($computed, (string) $signature);
    }
}
