@extends('layouts.app')

@section('title', 'Demandes de certification')

@section('content')
<div class="mx-auto max-w-7xl py-8">
  <div class="rounded-3xl border border-[#D4CABC] bg-white p-8 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-[#1F2E26]">Demandes de certification</h1>
        <p class="mt-2 text-sm text-[#5E6E52]">Validez les comptes certifiés payants pour les universités.</p>
      </div>
    </div>

    @if(session('status'))
      <div class="mt-6 rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm text-[#1F2E26]">
        {{ session('status') }}
      </div>
    @endif

    <div class="mt-8 space-y-4">
      @forelse($requests as $request)
        <div class="rounded-3xl border border-[#D4CABC] bg-[#FBF8F1] p-5">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#B8442E]">{{ ucfirst($request->status) }}</p>
              <h2 class="mt-2 text-xl font-semibold text-[#1F2E26]">{{ $request->user->name ?? 'Utilisateur supprimé' }}</h2>
              <p class="mt-1 text-sm text-[#5E6E52]">Université : {{ $request->university }} · Forfait : {{ $request->package }}</p>
              <p class="mt-2 text-sm text-[#5E6E52]">Paiement : {{ $request->payment_status }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
              @if($request->status === 'pending')
                <form action="{{ route('admin.certifications.approve', $request) }}" method="POST">
                  @csrf
                  <button type="submit" class="rounded-2xl bg-[#1F2E26] px-4 py-2 text-sm font-semibold text-white hover:bg-[#15201A]">Approuver</button>
                </form>
                <form action="{{ route('admin.certifications.reject', $request) }}" method="POST">
                  @csrf
                  <button type="submit" class="rounded-2xl border border-[#D4CABC] px-4 py-2 text-sm font-semibold text-[#1F2E26] hover:bg-[#F8F2E6]">Refuser</button>
                </form>
              @else
                <span class="rounded-2xl border border-[#D4CABC] bg-white px-4 py-2 text-sm text-[#1F2E26]">Traitée</span>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="rounded-3xl border border-dashed border-[#D4CABC] bg-[#FBF8F1] p-8 text-center text-sm text-[#5E6E52]">
          Aucune demande de certification pour le moment.
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
