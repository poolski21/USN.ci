<?php

namespace App\Services;

use Illuminate\Support\Str;

class PaymentGatewayService
{
    public function createCheckout(array $data): array
    {
        return [
            'provider' => config('services.payment.provider', 'mock'),
            'checkout_id' => Str::uuid()->toString(),
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'XOF',
            'status' => 'pending',
            'redirect_url' => route('certification.payment.callback', ['checkout_id' => Str::uuid()->toString()]),
        ];
    }

    public function verifyPayment(string $checkoutId): array
    {
        return [
            'checkout_id' => $checkoutId,
            'status' => 'succeeded',
            'provider' => config('services.payment.provider', 'mock'),
        ];
    }
}
