{{-- resources/views/friend-requests.blade.php --}}
@extends('layouts.app')

@section('title', 'Demandes d\'ami — USN')

@section('content')
  <div class="max-w-4xl mx-auto">
    <div class="rounded-3xl bg-white/90 border border-ardoise/20 p-6 shadow-sm">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-2xl font-semibold text-ardoise">Demandes d'ami</h1>
          <p class="text-sm text-gray-600 mt-1">{{ $pendingRequests->count() }} demande{{ $pendingRequests->count() !== 1 ? 's' : '' }} en attente</p>
        </div>
      </div>

      @if($pendingRequests->isEmpty())
        <div class="rounded-3xl bg-kraft-light border border-kraft-dark/40 p-8 text-center">
          <div class="mb-3 text-4xl">📭</div>
          <p class="text-sm text-gray-600">Vous n'avez aucune demande d'ami en attente pour le moment.</p>
          <a href="{{ route('search') }}" class="mt-4 inline-block rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors">
            Chercher des amis
          </a>
        </div>
      @else
        <div class="space-y-4">
          @foreach($pendingRequests as $friendRequest)
            @php
              $sender = $friendRequest->sender;
            @endphp
            <div class="friend-request-card rounded-2xl border border-ardoise/10 bg-white p-4 shadow-sm hover:shadow-md transition-shadow">
              <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <!-- Sender Info -->
                <a href="{{ route('profil.show', $sender->handle ?? $sender->id) }}" class="flex items-center gap-3 min-w-0 flex-1 hover:opacity-80 transition-opacity">
                  <div class="flex-shrink-0">
                    <img 
                      src="{{ $sender->avatar ? asset('storage/' . $sender->avatar) : 'https://via.placeholder.com/48' }}" 
                      alt="{{ $sender->name }}" 
                      class="w-12 h-12 rounded-full object-cover"
                    />
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-ardoise truncate">{{ $sender->name }}</p>
                    <p class="text-sm text-gray-600 truncate">{{ $sender->filiere ?? 'Filière inconnue' }} · {{ $sender->niveau ?? 'Année inconnue' }}</p>
                    @if($sender->universitaire_at)
                      <p class="text-xs text-gray-500">{{ $sender->universitaire_at }}</p>
                    @endif
                  </div>
                </a>

                <!-- Actions -->
                <div class="friend-request-actions flex flex-wrap items-center gap-2">
                  <form action="{{ route('friend.requests.accept', $friendRequest->id) }}" method="POST" class="friend-action-form inline">
                    @csrf
                    <button 
                      type="submit" 
                      class="rounded-full bg-moutarde px-4 py-2 text-sm font-semibold text-ardoise hover:bg-moutarde/90 transition-colors disabled:opacity-50"
                      title="Accepter cette demande d'ami"
                    >
                      Accepter
                    </button>
                  </form>
                  <form action="{{ route('friend.requests.decline', $friendRequest->id) }}" method="POST" class="friend-action-form inline">
                    @csrf
                    <button 
                      type="submit" 
                      class="rounded-full border border-ardoise/20 px-4 py-2 text-sm text-ardoise hover:bg-ardoise/5 transition-colors disabled:opacity-50"
                      title="Refuser cette demande d'ami"
                    >
                      Refuser
                    </button>
                  </form>
                </div>
              </div>

              <!-- Timestamp -->
              <p class="text-xs text-gray-500 mt-3">Reçue {{ $friendRequest->created_at->diffForHumans() }}</p>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    <!-- Quick Links -->
    <div class="mt-8 grid gap-4 sm:grid-cols-2">
      <a href="{{ route('search') }}" class="rounded-2xl border border-ardoise/10 bg-white/90 p-4 text-center hover:shadow-md transition-shadow">
        <p class="text-sm font-semibold text-ardoise">🔍 Chercher des amis</p>
      </a>
      <a href="{{ route('notifications') }}" class="rounded-2xl border border-ardoise/10 bg-white/90 p-4 text-center hover:shadow-md transition-shadow">
        <p class="text-sm font-semibold text-ardoise">🔔 Voir les notifications</p>
      </a>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.friend-action-form').forEach(function (form) {
          form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const card = form.closest('.friend-request-card');
            const actions = form.closest('.friend-request-actions');
            const button = form.querySelector('button');
            const formData = new FormData(form);

            button.disabled = true;

            try {
              const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': window.USN.csrfToken,
                  'Accept': 'application/json',
                },
              });

              if (!res.ok) throw new Error('Erreur réseau');
              const data = await res.json().catch(() => null);
              const state = data?.state;

              if (state === 'accepted') {
                // Remove the card with animation
                card.style.opacity = '0.5';
                setTimeout(() => {
                  card.style.animation = 'slideOut 0.3s ease-out';
                  setTimeout(() => card.remove(), 300);
                }, 300);
              } else if (state === 'declined') {
                // Remove the card with animation
                card.style.opacity = '0.5';
                setTimeout(() => {
                  card.style.animation = 'slideOut 0.3s ease-out';
                  setTimeout(() => card.remove(), 300);
                }, 300);
              } else {
                button.disabled = false;
              }
            } catch (err) {
              console.error(err);
              button.disabled = false;
              alert('Une erreur est survenue. Veuillez réessayer.');
            }
          });
        });
      });
    </script>
    <style>
      @keyframes slideOut {
        from { transform: translateX(0); }
        to { transform: translateX(100%); }
      }
    </style>
  @endpush
@endsection
