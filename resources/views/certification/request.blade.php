@extends('layouts.app')

@section('title', 'Demander un compte certifié')

@section('content')
<div class="mx-auto max-w-4xl py-8">
  <div class="rounded-3xl border border-[#D4CABC] bg-white p-8 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#B8442E]">Certification premium</p>
        <h1 class="mt-2 text-3xl font-bold text-[#1F2E26]">Obtenez un compte certifié pour votre université</h1>
        <p class="mt-3 max-w-2xl text-sm text-[#5E6E52]">Cette option vous donne un badge de vérification visible sur votre profil, une meilleure visibilité et un accès premium au réseau.</p>
      </div>
      <div class="rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm text-[#5E6E52]">
        <p class="font-semibold text-[#1F2E26]">Prix</p>
        <p class="mt-1 text-xl font-bold text-[#1F2E26]">25 000 FCFA</p>
        <p class="text-xs">Paiement sécurisé et vérification manuelle</p>
      </div>
    </div>

    @if(session('status'))
      <div class="mt-6 rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm text-[#1F2E26]">
        {{ session('status') }}
      </div>
    @endif
    @if(session('error') || request('error'))
      <div class="mt-6 rounded-2xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') ?? __('Une erreur est survenue pendant le paiement.') }}
      </div>
    @endif

    <form action="{{ route('certification.store') }}" method="POST" class="mt-8 space-y-5">
      @csrf
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label for="university" class="mb-2 block text-sm font-medium text-[#1F2E26]">Université</label>
          <input id="university" name="university" type="text" value="{{ old('university', auth()->user()->universite ?? '') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" placeholder="Ex. USN" required>
        </div>
        <div>
          <label for="package" class="mb-2 block text-sm font-medium text-[#1F2E26]">Forfait</label>
          <select id="package" name="package" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
            <option value="standard">Standard — 25 000 FCFA</option>
            <option value="premium">Premium — 45 000 FCFA</option>
          </select>
        </div>
      </div>

      <div class="rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] p-4 text-sm text-[#5E6E52]">
        <p class="font-semibold text-[#1F2E26]">Ce que vous recevez</p>
        <ul class="mt-3 list-disc space-y-2 pl-5">
          <li>Badge “Compte certifié” affiché sur votre profil</li>
          <li>Vérification visible par les autres membres de la communauté</li>
          <li>Priorité dans l’affichage des profils universitaires</li>
        </ul>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('profil.show') }}" class="rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm font-medium text-[#1F2E26] hover:bg-[#F8F2E6]">Annuler</a>
        <button type="submit" class="rounded-2xl bg-[#1F2E26] px-5 py-3 text-sm font-semibold text-white hover:bg-[#15201A]">Payer et envoyer la demande</button>
      </div>
    </form>
  </div>
</div>
@endsection
