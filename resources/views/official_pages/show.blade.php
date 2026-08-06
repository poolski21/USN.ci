@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="mx-auto max-w-6xl py-8">
  <div class="rounded-3xl overflow-hidden border border-[#D4CABC] bg-white shadow-sm">
    <div class="relative h-72 overflow-hidden bg-[#F3F1EB]">
      <img src="{{ $page->cover_image_url }}" alt="Couverture de {{ $page->title }}" class="h-full w-full object-cover">
      <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-4 bg-gradient-to-t from-black/60 to-transparent px-6 py-4 text-white">
        <div>
          <h1 class="text-3xl font-bold">{{ $page->title }}</h1>
          <p class="text-sm text-white/80">Page officielle certifiée par {{ $page->user->prenom }} {{ $page->user->nom }}</p>
        </div>
        @if(auth()->id() === $page->user_id)
          <a href="{{ route('official_pages.edit', $page->slug) }}" class="rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm hover:bg-white/20">Modifier</a>
        @endif
      </div>
    </div>

    <div class="grid gap-6 px-6 py-8 lg:grid-cols-[240px_1fr]">
      <aside class="space-y-6 rounded-3xl border border-[#D4CABC] bg-[#F8F2E6] p-6">
        <div class="flex flex-col items-center gap-3 text-center">
          <img src="{{ $page->avatar_image_url }}" alt="Avatar de {{ $page->title }}" class="h-24 w-24 rounded-full border border-white object-cover shadow-sm">
          <div class="space-y-1">
            <p class="text-sm text-[#5E6E52]">Page officielle certifiée</p>
            <x-certified-badge label="Officiel" />
          </div>
        </div>
        <div class="space-y-2 text-sm text-[#5E6E52]">
          <p><span class="font-semibold text-[#1F2E26]">Propriétaire</span></p>
          <p>{{ $page->user->prenom }} {{ $page->user->nom }}</p>
          <p class="text-xs text-[#7D8B77]">{{ $page->user->universite ?? 'Université non renseignée' }}</p>
        </div>
      </aside>

      <div class="space-y-6">
        @if(session('status'))
          <div class="rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] p-4 text-sm text-[#1F2E26]">
            {{ session('status') }}
          </div>
        @endif

        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6">
          <h2 class="text-2xl font-semibold text-[#1F2E26]">À propos</h2>
          <p class="mt-4 text-sm leading-relaxed text-[#5E6E52]">{{ $page->description ?? 'Aucune description n’a encore été ajoutée pour cette page officielle.' }}</p>
        </div>

        <div class="rounded-3xl border border-[#D4CABC] bg-white p-6">
          <h2 class="text-2xl font-semibold text-[#1F2E26]">Contact</h2>
          <p class="mt-3 text-sm text-[#5E6E52]">Pour contacter cette page officielle, envoyez un message direct à {{ $page->user->prenom }} via son profil.</p>
          <a href="{{ route('profil.show', $page->user->handle ?? $page->user->id) }}" class="mt-4 inline-flex rounded-2xl bg-[#1F2E26] px-4 py-3 text-sm font-semibold text-white hover:bg-[#15201A]">Voir le profil du propriétaire</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
