<?php

namespace App\Http\Controllers;

use App\Models\CertificationRequest;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificationController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        return view('certification.request', compact('user'));
    }

    public function store(Request $request, PaymentGatewayService $paymentGatewayService)
    {
        $validated = $request->validate([
            'university' => ['required', 'string', 'max:255'],
            'package' => ['required', 'in:standard,premium'],
        ]);

        $user = Auth::user();
        $amount = $validated['package'] === 'premium' ? 45000 : 25000;
        $checkout = $paymentGatewayService->createCheckout([
            'amount' => $amount,
            'currency' => 'XOF',
        ]);

        $requestRecord = CertificationRequest::create([
            'user_id' => $user->id,
            'university' => $validated['university'],
            'package' => $validated['package'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => 'Paiement initié via ' . $checkout['provider'] . '. Checkout: ' . $checkout['checkout_id'],
        ]);

        $user->forceFill([
            'certification_status' => 'pending',
            'certification_package' => $validated['package'],
            'certified_university' => $validated['university'],
        ])->save();

        return redirect()->away($checkout['redirect_url'])->with('status', 'Votre paiement a été initié.');
    }

    public function paymentCallback(Request $request, PaymentGatewayService $paymentGatewayService, string $checkoutId)
    {
        $result = $paymentGatewayService->verifyPayment($checkoutId);

        if ($result['status'] !== 'succeeded') {
            return redirect()->route('certification.request')->with('error', 'Le paiement n’a pas abouti.');
        }

        $requestRecord = CertificationRequest::where('notes', 'like', '%Checkout: ' . $checkoutId . '%')->latest()->first();

        if ($requestRecord) {
            $requestRecord->update(['payment_status' => 'paid']);
        }

        return redirect()->route('certification.request')->with('status', 'Paiement validé. Votre demande est en cours de traitement.');
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

    public function approve(CertificationRequest $certificationRequest)
    {
        $user = Auth::user();

        if (! $user || ($user->role ?? null) !== 'admin') {
            abort(403);
        }

        $certificationRequest->update([
            'status' => 'approved',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $certificationRequest->user->forceFill([
            'is_certified' => true,
            'certification_status' => 'approved',
            'certified_university' => $certificationRequest->university,
            'certification_package' => $certificationRequest->package,
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
