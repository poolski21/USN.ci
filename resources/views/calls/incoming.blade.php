@extends('layouts.app')

@section('title', 'Appels entrants — USN')

@section('content')
  <div class="container mx-auto mt-6">
    <div class="rounded-3xl bg-white/95 border border-ardoise/20 p-6 shadow-sm dark:bg-slate-950/90 dark:border-slate-700">
      <h1 class="text-2xl font-semibold text-ardoise mb-4">Appels entrants</h1>

      @if($sessions->isEmpty())
        <div class="rounded-3xl border border-ardoise/10 bg-kraft-light p-6 text-sm text-gray-600">
          Vous n’avez aucun appel entrant en attente.
        </div>
      @else
        <div class="space-y-4">
          @foreach($sessions as $session)
            <div class="rounded-3xl border border-ardoise/20 bg-white p-5 shadow-sm dark:bg-slate-900/90 dark:border-slate-700">
              <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-500 uppercase tracking-[.18em]">{{ ucfirst($session->type) }} entrant</p>
                  <h2 class="text-xl font-semibold text-ardoise">{{ $session->caller->prenom }} {{ $session->caller->nom }}</h2>
                  <p class="text-sm text-gray-500">{{ $session->caller->handle ? '@' . $session->caller->handle : 'ID ' . $session->caller->id }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                  <a href="{{ route('messages.call', ['session' => $session->id]) }}" class="rounded-full border border-ardoise/20 bg-white px-4 py-2 text-sm font-semibold text-ardoise hover:bg-ardoise/5 transition-colors">Répondre</a>
                  <form action="{{ route('messages.call.reject', ['session' => $session->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-full bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Ignorer</button>
                  </form>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endsection
