<?php

namespace App\Http\Controllers;

use App\Models\CertificationRequest;
use App\Models\User;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CertificationController extends Controller
{
    public function show()
    {
        return view('certification.request');
    }

    public function store(Request $request, PaymentGatewayService $paymentGatewayService)
    {
        $validated = $request->validate([
            'university' => ['required', 'string', 'max:255'],
            'package' => ['required', 'in:standard,premium'],
        ]);

        $user = Auth::user();
        $amount = $validated['package'] === 'premium' ? 45000 : 25000;

        // create a reference and persist the certification request
        $reference = $paymentGatewayService->generateReference();

        $certificationRequest = CertificationRequest::create([
            'user_id' => $user->id,
            'reference' => $reference,
            'university' => $validated['university'],
            'package' => $validated['package'],
            'amount' => $amount,
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => 'Demande créée. Paiement en attente.',
        ]);

        // redirect to the inline widget payment page where the client will open the widget
        return redirect()->route('certification.payment', $certificationRequest);
    }

    public function payment(CertificationRequest $certificationRequest)
    {
        $user = Auth::user();

        abort_unless($certificationRequest->user_id === $user->id, 403);
        abort_unless($certificationRequest->payment_status === 'pending', 403);

        // Provide public key and data for inline widget initialization
        return view('certification.index', [
            'publicKey' => config('services.kadevpay.public_key'),
            'checkout' => [
                'reference' => $certificationRequest->reference,
                'amount' => $certificationRequest->amount,
                'currency' => 'XOF',
                'callback_url' => route('certification.callback'),
            ],
            'certificationRequest' => $certificationRequest,
            'user' => $user,
        ]);
    }

    public function callback(Request $request)
    {
        // Widget redirect; do not mark paid here—webhook will confirm
        return redirect()->route('certification.request')->with('status', 'Vérification en cours. Merci de patienter pendant la confirmation du paiement.');
    }

    public function webhook(Request $request, PaymentGatewayService $paymentGatewayService)
    {
        $signature = $request->header('X-KadevPay-Signature') ?? $request->header('x-kadevpay-signature');
        $raw = $request->getContent();

        if (! $paymentGatewayService->verifyWebhookSignature($raw, (string) $signature)) {
            Log::warning('KadevPay webhook signature invalid', ['headers' => $request->headers->all()]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $payload = json_decode($raw, true) ?: [];
        $event = data_get($payload, 'event');
        $data = data_get($payload, 'data', []);
        $reference = data_get($data, 'reference') ?? data_get($payload, 'reference');

        if (! $reference) {
            Log::warning('KadevPay webhook missing reference', $payload);
            return response()->json(['message' => 'Missing reference'], 200);
        }

        $record = CertificationRequest::query()->where('reference', $reference)->latest()->first();
        if (! $record) {
            Log::warning('KadevPay webhook: request not found', ['reference' => $reference]);
            return response()->json(['message' => 'Request not found'], 200);
        }

        if ($record->payment_status === 'paid') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Only handle payment.success events where data.status === 'paid'
        if ($event !== 'payment.success' || data_get($data, 'status') !== 'paid') {
            Log::info('KadevPay webhook ignored', ['reference' => $reference, 'event' => $event, 'data' => $data]);
            return response()->json(['message' => 'Ignored'], 200);
        }

        // re-verify transaction with Kadev Pay API
        $status = $paymentGatewayService->verifyTransaction($reference);
        if (strtolower((string) $status) !== 'paid') {
            Log::warning('KadevPay webhook: verification mismatch', ['reference' => $reference, 'remote_status' => $status]);
            return response()->json(['message' => 'Not confirmed'], 200);
        }

        // verify amount & currency if present
        $providerAmount = (int) data_get($data, 'amount', $record->amount);
        $providerCurrency = strtoupper((string) data_get($data, 'currency', 'XOF'));
        if ($providerAmount !== (int) $record->amount || $providerCurrency !== 'XOF') {
            Log::warning('KadevPay webhook amount/currency mismatch', ['reference' => $reference, 'provider' => compact('providerAmount', 'providerCurrency'), 'record_amount' => $record->amount]);
            return response()->json(['message' => 'Amount mismatch'], 200);
        }

        // mark paid and certify user
        DB::transaction(function () use ($record, $data) {
            $providerTxId = data_get($data, 'transaction_id') ?? data_get($data, 'transactionId');
            $record->update([
                'payment_status' => 'paid',
                'provider_transaction_id' => $providerTxId,
                'status' => 'pending',
                'notes' => 'Paiement confirmé via KadevPay webhook.',
            ]);

            $user = $record->user;
            if ($user) {
                $user->forceFill([
                    'is_certified' => true,
                    'certified_via' => 'kadevpay',
                    'certified_at' => now(),
                    'certified_until' => now()->addMonth(),
                ])->save();
            }
        });

        return response()->json(['message' => 'Webhook processed'], 200);
    }

    public function showAdminCertifications()
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== 'admin') {
            abort(403);
        }

        $requests = CertificationRequest::with('user')->latest()->get();

        return view('admin.certifications', compact('requests'));
    }

    public function approve(string $certificationRequest)
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== 'admin') {
            abort(403);
        }

        $request = CertificationRequest::findOrFail($certificationRequest);

        $request->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $certifiedUser = User::findOrFail($request->user_id);
        $certifiedUser->forceFill([
            'is_certified' => true,
            'certification_status' => 'approved',
            'certified_university' => $request->university,
            'certification_package' => $request->package,
            'certified_via' => 'admin',
            'certified_at' => now(),
            'certified_until' => now()->addMonth(),
        ])->save();

        return redirect()->route('admin.certifications')->with('status', 'La demande a été approuvée.');
    }

    public function reject(CertificationRequest $certificationRequest)
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== 'admin') {
            abort(403);
        }

        $certificationRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $certificationRequest->user->forceFill([
            'is_certified' => false,
            'certification_status' => 'rejected',
            'certified_university' => null,
            'certification_package' => null,
        ])->save();

        return redirect()->route('admin.certifications')->with('status', 'La demande a été refusée.');
    }
}
