@extends('layouts.app')

@section('title', 'Paiement Certification')

@section('content')
<div class="mx-auto max-w-2xl py-8">
  <div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold">Paiement de la certification</h2>
    <p class="mt-2">Forfait : {{ ucfirst($certificationRequest->package) }}</p>
    <p class="mt-2">Montant : {{ number_format($checkout['amount'],0,',',' ') }} {{ $checkout['currency'] }}</p>
    <button id="kadevpay-button" class="mt-4 px-4 py-2 bg-green-600 text-white rounded">Payer via Kadev Pay</button>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://pay.kadev.ci/js/v1/kadev-pay.js"></script>
<script>
  const publicKey = '{{ config('services.kadevpay.public_key') }}';
  const checkout = @json($checkout);
  const user = @json($user);

  document.getElementById('kadevpay-button')?.addEventListener('click', function () {
    KadevPay.checkout({
      public_key: publicKey,
      amount: checkout.amount,
      currency: checkout.currency,
      email: user.email,
      name: user.name,
      method: 'momo',
      callback_url: checkout.callback_url,
      metadata: { reference: checkout.reference },
    });
  });
</script>
@endpush
