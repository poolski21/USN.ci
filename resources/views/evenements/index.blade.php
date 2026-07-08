@extends('layouts.app')

@section('title', 'Événements')

@section('content')
<div class="mx-auto max-w-5xl py-8">
  <div class="mb-6 flex items-center justify-between">
    <div>
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#5E6E52]">Événements</p>
      <h1 class="text-2xl font-bold text-[#1F2E26]">Découvrez les événements du campus</h1>
    </div>
    <a href="{{ route('evenements.create') }}" class="rounded-full bg-[#1F2E26] px-5 py-2.5 text-sm font-semibold text-[#F7F1E3] hover:bg-[#5E6E52]">
      + Créer un événement
    </a>
  </div>

  @if(session('status'))
    <div class="mb-4 rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm text-[#1F2E26]">
      {{ session('status') }}
    </div>
  @endif

  <div class="space-y-4">
    @forelse($evenements as $evenement)
      <article class="rounded-[24px] border border-[#D4CABC] bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:justify-between">
          <div>
            <h2 class="text-xl font-semibold text-[#1F2E26]">{{ $evenement->titre }}</h2>
            <p class="mt-2 text-sm text-[#5E6E52]">{{ Str::limit($evenement->description, 180) }}</p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs text-[#5E6E52]">
              <span class="rounded-full bg-[#F8F2E6] px-3 py-1">{{ $evenement->categorie }}</span>
              <span class="rounded-full bg-[#F8F2E6] px-3 py-1">{{ ucfirst($evenement->visibilite) }}</span>
              <span class="rounded-full bg-[#F8F2E6] px-3 py-1">{{ ucfirst($evenement->mode) }}</span>
            </div>
          </div>
          @if($evenement->image_couverture)
            <div class="mt-3 md:mt-0 md:ml-4 flex-shrink-0">
              <div class="overflow-hidden rounded-lg" style="height:120px; width:200px; border:1px solid rgba(0,0,0,0.04);">
                <img src="{{ asset('storage/'.$evenement->image_couverture) }}" alt="Couverture" style="height:120px; width:200px; object-fit:cover; object-position:center; display:block;" />
              </div>
            </div>
          @endif
          <div class="min-w-[180px] rounded-2xl bg-[#F8F2E6] p-4 text-sm text-[#1F2E26]">
            <p class="font-semibold">Début</p>
            <p>{{ $evenement->date_debut->format('d/m/Y H:i') }}</p>
            <p class="mt-2 font-semibold">Fin</p>
            <p>{{ $evenement->date_fin->format('d/m/Y H:i') }}</p>
          </div>
        </div>

          <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-[#D4CABC] pt-4 text-sm">
          <a href="{{ route('evenements.show', $evenement) }}" class="rounded-full bg-[#5E6E52] px-4 py-2 text-white">Voir l’événement</a>
          <span>{{ $evenement->likes_count }} j’aime</span>
          <a href="{{ route('evenements.show', $evenement) }}#comments" class="text-[#5E6E52] underline">{{ $evenement->comments_count }} commentaires</a>
          <span>{{ $evenement->shares_count }} partages</span>
        </div>
      </article>
    @empty
      <div class="rounded-[24px] border border-[#D4CABC] bg-white p-8 text-center text-sm text-[#5E6E52]">
        Aucun événement pour le moment.
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $evenements->links() }}
  </div>
</div>
@endsection
