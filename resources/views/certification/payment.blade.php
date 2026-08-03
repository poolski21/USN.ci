@extends('layouts.app')

@section('title', 'Paiement Certification')

@section('content')
<div class="mx-auto max-w-4xl py-8">
  <div class="rounded-3xl border border-[#D4CABC] bg-white p-8 shadow-sm">
    <div class="text-center">
      <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#B8442E]">Confirmation de paiement</p>
      <h1 class="mt-4 text-3xl font-bold text-[#1F2E26]">Vous êtes sur le point de finaliser votre demande</h1>
      <p class="mt-3 max-w-2xl mx-auto text-sm text-[#5E6E52]">Cliquez sur le bouton ci-dessous pour ouvrir la page de paiement Flutterwave et compléter votre transaction.</p>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-2">
      <div class="rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] p-6">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1F2E26]">Détails de la commande</p>
        <dl class="mt-4 space-y-3 text-sm text-[#5E6E52]">
          <div>
            <dt class="font-semibold text-[#1F2E26]">Université</dt>
            <dd>{{ $certificationRequest->university }}</dd>
          </div>
          <div>
            <dt class="font-semibold text-[#1F2E26]">Forfait</dt>
            <dd class="capitalize">{{ $certificationRequest->package }}</dd>
          </div>
          <div>
            <dt class="font-semibold text-[#1F2E26]">Montant</dt>
            <dd>{{ number_format($certificationRequest->amount, 0, ',', ' ') }} FCFA</dd>
          </div>
          <div>
            <dt class="font-semibold text-[#1F2E26]">Référence</dt>
            <dd>{{ $checkout['reference'] }}</dd>
          </div>
        </dl>
      </div>

      <div class="rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] p-6">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#1F2E26]">Informations de paiement</p>
        <form id="payment-form" class="mt-4 space-y-4">
          <div>
            <label for="payer-name" class="mb-2 block text-sm font-medium text-[#1F2E26]">Nom complet</label>
            <input id="payer-name" name="payer_name" type="text" value="{{ $user->name }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FFFFFF] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
          </div>
          <div>
            <label for="payer-email" class="mb-2 block text-sm font-medium text-[#1F2E26]">Adresse email</label>
            <input id="payer-email" name="payer_email" type="email" value="{{ $user->email }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FFFFFF] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
          </div>
          <div>
            <label for="payer-phone" class="mb-2 block text-sm font-medium text-[#1F2E26]">Téléphone</label>
            <input id="payer-phone" name="payer_phone" type="tel" value="" placeholder="+221 77 123 45 67" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FFFFFF] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
          </div>
          <div>
            <label for="payment-method" class="mb-2 block text-sm font-medium text-[#1F2E26]">Moyen de paiement</label>
            <select id="payment-method" name="payment_method" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FFFFFF] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
              <option value="" disabled selected>Choisissez un moyen de paiement</option>
              <option value="orange_money">Orange Money</option>
              <option value="moov_money">Moov Money</option>
              <option value="mtn_money">MTN Money</option>
              <option value="wave">Wave</option>
              <option value="card_iban">Carte / IBAN</option>
            </select>
          </div>

          <div id="payment-errors" class="hidden rounded-2xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

          <button id="flutterwave-button" type="button" class="mt-4 w-full rounded-2xl bg-[#1F2E26] px-5 py-3 text-sm font-semibold text-white hover:bg-[#15201A]">Payer et remplir le formulaire</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
  const publicKey = '{{ config('services.flutterwave.public_key') }}';
  const checkout = @json($checkout);

  const form = document.getElementById('payment-form');
  const button = document.getElementById('flutterwave-button');
  const errorBox = document.getElementById('payment-errors');

  const showError = (message) => {
    if (!errorBox) return;
    errorBox.textContent = message;
    errorBox.classList.remove('hidden');
  };

  const validateForm = () => {
    const name = document.getElementById('payer-name')?.value.trim();
    const email = document.getElementById('payer-email')?.value.trim();
    const phone = document.getElementById('payer-phone')?.value.trim();
    const method = document.getElementById('payment-method')?.value;

    if (!name || !email || !phone || !method) {
      showError('Veuillez renseigner tous les champs de paiement.');
      return null;
    }

    return { name, email, phone, method };
  };

  button?.addEventListener('click', function () {
    const customer = validateForm();
    if (!customer) {
      return;
    }

    const paymentOptions = {
      orange_money: 'mobilemoney',
      moov_money: 'mobilemoney',
      mtn_money: 'mobilemoney',
      wave: 'mobilemoney',
      card_iban: 'card,banktransfer',
    }[customer.method] || 'card,mobilemoney,banktransfer';

    FlutterwaveCheckout({
      public_key: publicKey,
      tx_ref: checkout.reference,
      amount: checkout.amount,
      currency: checkout.currency,
      country: 'SN',
      payment_options: paymentOptions,
      customer: {
        email: customer.email,
        name: customer.name,
        phone_number: customer.phone,
      },
      customizations: {
        title: 'Certification USN',
        description: 'Paiement de votre demande de compte certifié',
        logo: '{{ asset('favicon.svg') }}',
      },
      callback: function (data) {
        if (data?.status === 'successful') {
          window.location.href = '{{ route('certification.callback') }}?transaction_id=' + encodeURIComponent(data.transaction_id) + '&tx_ref=' + encodeURIComponent(data.tx_ref) + '&status=' + encodeURIComponent(data.status);
        } else {
          showError('Le paiement a échoué. Veuillez réessayer.');
        }
      },
      onclose: function () {
        showError('Le paiement a été fermé avant validation.');
      }
    });
  });
</script>
@endpush
