@extends('layouts.app')

@section('title', $evenement->titre)

@section('content')
<div class="mx-auto max-w-5xl py-8">
  <div class="rounded-[28px] border border-[#D4CABC] bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#5E6E52]">Événement</p>
        <h1 class="text-2xl font-bold text-[#1F2E26]">{{ $evenement->titre }}</h1>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <form action="{{ route('evenements.like', $evenement) }}" method="POST" class="event-like-form">
          @csrf
          <button type="submit" data-liked="{{ $evenement->likedByUser(auth()->id()) ? '1' : '0' }}" class="rounded-full border border-[#D4CABC] px-4 py-2 text-sm font-medium text-[#1F2E26] hover:bg-[#F8F2E6]">
            <i class="ti ti-heart{{ $evenement->likedByUser(auth()->id()) ? '-filled' : '' }}"></i> <span class="event-like-label">J’aime</span>
          </button>
        </form>
        <form action="{{ route('evenements.share', $evenement) }}" method="POST" class="event-share-form">
          @csrf
          <button type="submit" class="rounded-full border border-[#D4CABC] px-4 py-2 text-sm font-medium text-[#1F2E26] hover:bg-[#F8F2E6]">
            <i class="ti ti-share-3"></i> Partager
          </button>
        </form>
        <span class="event-like-counter rounded-full bg-[#F8F2E6] px-3 py-2 text-sm text-[#5E6E52]">{{ $evenement->likes_count }} j’aime</span>
        <span class="event-comment-counter rounded-full bg-[#F8F2E6] px-3 py-2 text-sm text-[#5E6E52]">{{ $evenement->comments_count }} commentaires</span>
        <span class="event-share-counter rounded-full bg-[#F8F2E6] px-3 py-2 text-sm text-[#5E6E52]">{{ $evenement->shares_count }} partages</span>
      </div>
    </div>

    @if($evenement->image_couverture)
      <div class="mt-6 overflow-hidden rounded-[20px] shadow-sm" style="border:1px solid rgba(0,0,0,0.04);">
        <img src="{{ asset('storage/'.$evenement->image_couverture) }}" alt="Couverture de l'événement" class="w-full object-cover" style="height:380px; width:100%; object-fit:cover; object-position:center; display:block;" />
      </div>
    @endif

    @if(session('status'))
      <div class="mt-4 rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm text-[#1F2E26]">
        {{ session('status') }}
      </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
      <div class="space-y-4">
        <div class="rounded-[24px] bg-[#F8F2E6] p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Description</h2>
          <p class="mt-3 text-sm leading-7 text-[#5E6E52]">{{ $evenement->description }}</p>
        </div>

        <div class="rounded-[24px] border border-[#D4CABC] p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Informations</h2>
          <div class="mt-4 grid gap-3 text-sm text-[#5E6E52] sm:grid-cols-2">
            <div><span class="font-semibold text-[#1F2E26]">Catégorie :</span> {{ $evenement->categorie }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Visibilité :</span> {{ ucfirst($evenement->visibilite) }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Date début :</span> {{ $evenement->date_debut->format('d/m/Y H:i') }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Date fin :</span> {{ $evenement->date_fin->format('d/m/Y H:i') }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Mode :</span> {{ ucfirst($evenement->mode) }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Lieu :</span> {{ $evenement->lieu ?? '—' }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Lien :</span> {{ $evenement->lien_ligne ?? '—' }}</div>
            <div><span class="font-semibold text-[#1F2E26]">Places :</span> {{ $evenement->places_max ?? 'Illimité' }}</div>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <div id="comments" class="rounded-[24px] border border-[#D4CABC] bg-[#F8F2E6] p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Commentaires</h2>
          <form action="{{ route('evenements.comment', $evenement) }}" method="POST" class="event-comment-form mt-4 space-y-3">
            @csrf
            <textarea name="contenu" rows="3" required maxlength="1000" class="w-full rounded-2xl border border-[#D4CABC] bg-white px-4 py-3 text-sm" placeholder="Votre commentaire..."></textarea>
            <button type="submit" class="rounded-full bg-[#1F2E26] px-4 py-2 text-sm font-semibold text-[#F7F1E3]">Commenter</button>
          </form>

          <div class="mt-5 space-y-3 event-comments-list">
            @forelse($evenement->comments as $comment)
              <div class="rounded-2xl bg-white p-3 text-sm">
                <div class="flex items-center justify-between gap-2">
                  <p class="font-semibold text-[#1F2E26]">{{ trim(($comment->user->prenom ?? '') . ' ' . ($comment->user->nom ?? '')) ?: 'Utilisateur' }}</p>
                  <span class="text-xs text-[#5E6E52]">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="mt-1 text-[#5E6E52]">{{ $comment->contenu }}</p>
              </div>
            @empty
              <p class="text-sm text-[#5E6E52]">Aucun commentaire pour l’instant.</p>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.event-like-form').forEach(function (form) {
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const button = form.querySelector('button');
        const label = form.querySelector('.event-like-label');
        const icon = form.querySelector('i');
        const formData = new FormData(form);

        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        if (response.ok) {
          const data = await response.json().catch(() => null);
          if (data?.liked !== undefined) {
            const liked = Boolean(data.liked);
            button?.classList.toggle('bg-[#F8F2E6]', liked);
            button?.classList.toggle('text-[#B8442E]', liked);
            if (icon) {
              icon.className = liked ? 'ti ti-heart-filled' : 'ti ti-heart';
            }
            if (label) {
              label.textContent = liked ? 'J’aime' : 'J’aime';
            }
            const counter = document.querySelector('.event-like-counter');
            if (counter) {
              counter.textContent = `${data.likesCount} j’aime`;
            }
          }
        }
      });
    });

    document.querySelectorAll('.event-share-form').forEach(function (form) {
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        if (response.ok) {
          const data = await response.json().catch(() => null);
          const counter = document.querySelector('.event-share-counter');
          if (counter && data?.sharesCount !== undefined) {
            counter.textContent = `${data.sharesCount} partages`;
          }
        }
      });
    });

    document.querySelectorAll('.event-comment-form').forEach(function (form) {
      form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        const textarea = form.querySelector('textarea');
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        if (response.ok) {
          const data = await response.json().catch(() => null);
          const counter = document.querySelector('.event-comment-counter');
          const list = document.querySelector('.event-comments-list');
          if (counter && data?.commentsCount !== undefined) {
            counter.textContent = `${data.commentsCount} commentaires`;
          }
          if (textarea) {
            textarea.value = '';
          }
          if (list && textarea?.value === '') {
            const item = document.createElement('div');
            item.className = 'rounded-2xl bg-white p-3 text-sm';
            item.innerHTML = `<div class="flex items-center justify-between gap-2"><p class="font-semibold text-[#1F2E26]">Vous</p><span class="text-xs text-[#5E6E52]">À l’instant</span></div><p class="mt-1 text-[#5E6E52]">${(formData.get('contenu') || '').toString()}</p>`;
            list.prepend(item);
          }
        }
      });
    });
  });
</script>
@endpush
