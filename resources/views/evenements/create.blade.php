@extends('layouts.app')

@section('title', 'Créer un événement')

@section('content')
<div class="min-h-screen bg-[#F7F1E3] px-4 py-8 text-[#1F2E26]">
  <div class="mx-auto max-w-5xl rounded-[28px] border border-[#D4CABC] bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-6 flex flex-col gap-3 border-b border-[#D4CABC] pb-5 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#5E6E52]">Nouvel événement</p>
        <h1 class="mt-1 text-2xl font-bold text-[#1F2E26]">Créer un événement universitaire</h1>
        <p class="mt-2 max-w-2xl text-sm text-[#5E6E52]">Organisez une conférence, une soirée, un sport, ou toute autre activité pour votre campus.</p>
      </div>
      <a href="{{ route('profil.show', auth()->user()->handle) }}" class="inline-flex items-center justify-center rounded-full border border-[#D4CABC] px-4 py-2 text-sm font-medium text-[#1F2E26] hover:bg-[#F7F1E3]">
        <i class="ti ti-arrow-left mr-2"></i> Retour au profil
      </a>
    </div>

    <form action="{{ route('evenements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
      @csrf

      <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
          <div>
            <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Titre de l'événement <span class="text-red-500">*</span></label>
            <input type="text" name="titre" value="{{ old('titre') }}" required class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40" placeholder="Ex. Forum emploi USN 2026">
            @error('titre')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" required class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40" placeholder="Décrivez l'événement, le thème, les objectifs...">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Catégorie</label>
              <select name="categorie" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40">
                @foreach(['Conférence','Soirée/Fête','Sport','Culturel','Académique','Association étudiante','Networking','Autre'] as $categorie)
                  <option value="{{ $categorie }}" {{ old('categorie') === $categorie ? 'selected' : '' }}>{{ $categorie }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Image de couverture</label>
              <input type="file" name="image_couverture" accept="image/*" class="w-full rounded-2xl border border-dashed border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
              @error('image_couverture')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>

        <div class="rounded-[24px] border border-[#D4CABC] bg-[#F8F2E6] p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Visibilité</h2>
          <div class="mt-4 space-y-3">
            <label class="flex items-center gap-2 text-sm">
              <input type="radio" name="visibilite" value="public" {{ old('visibilite', 'public') === 'public' ? 'checked' : '' }}>
              <span>Public</span>
            </label>
            <label class="flex items-center gap-2 text-sm">
              <input type="radio" name="visibilite" value="prive" {{ old('visibilite') === 'prive' ? 'checked' : '' }}>
              <span>Privé</span>
            </label>
          </div>

          <div id="restriction-block" class="mt-4 {{ old('visibilite') === 'prive' ? '' : 'hidden' }}">
            <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Restreindre à</label>
            <select name="restriction_type" class="w-full rounded-2xl border border-[#D4CABC] bg-white px-4 py-3 text-sm">
              <option value="">Sélectionner</option>
              <option value="groupe" {{ old('restriction_type') === 'groupe' ? 'selected' : '' }}>Groupe existant</option>
              <option value="filiere" {{ old('restriction_type') === 'filiere' ? 'selected' : '' }}>Filière / promo</option>
              <option value="invites" {{ old('restriction_type') === 'invites' ? 'selected' : '' }}>Invités choisis</option>
            </select>
            <input type="text" name="restriction_id" value="{{ old('restriction_id') }}" class="mt-3 w-full rounded-2xl border border-[#D4CABC] bg-white px-4 py-3 text-sm" placeholder="ID du groupe / filière / liste d’invités">
          </div>
        </div>
      </section>

      <section class="grid gap-6 lg:grid-cols-[1fr_1fr]">
        <div class="rounded-[24px] border border-[#D4CABC] bg-white p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Date et lieu</h2>
          <div class="mt-4 space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Date et heure de début <span class="text-red-500">*</span></label>
              <input type="datetime-local" name="date_debut" value="{{ old('date_debut') }}" required class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Date et heure de fin <span class="text-red-500">*</span></label>
              <input type="datetime-local" name="date_fin" value="{{ old('date_fin') }}" required class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
            </div>
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" id="all-day" name="all_day">
              <span>Toute la journée</span>
            </label>
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Mode</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm">
                  <input type="radio" name="mode" value="presentiel" {{ old('mode', 'presentiel') === 'presentiel' ? 'checked' : '' }}>
                  <span>Présentiel</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                  <input type="radio" name="mode" value="en_ligne" {{ old('mode') === 'en_ligne' ? 'checked' : '' }}>
                  <span>En ligne</span>
                </label>
              </div>
            </div>
            <div id="lieu-block">
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Lieu</label>
              <input type="text" name="lieu" value="{{ old('lieu') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm" placeholder="Campus, salle, adresse...">
            </div>
            <div id="lien-block" class="hidden">
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Lien de connexion</label>
              <input type="url" name="lien_ligne" value="{{ old('lien_ligne') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm" placeholder="https://...">
            </div>
          </div>
        </div>

        <div class="rounded-[24px] border border-[#D4CABC] bg-white p-5">
          <h2 class="text-lg font-semibold text-[#1F2E26]">Participation</h2>
          <div class="mt-4 space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Nombre de places max</label>
              <input type="number" min="1" name="places_max" value="{{ old('places_max') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm" placeholder="Laisser vide si illimité">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Inscription requise</label>
              <select name="inscription_requise" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
                <option value="1" {{ old('inscription_requise', '1') == '1' ? 'selected' : '' }}>Oui</option>
                <option value="0" {{ old('inscription_requise') == '0' ? 'selected' : '' }}>Non</option>
              </select>
            </div>
            <div id="validation-block">
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Validation</label>
              <select name="validation_type" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
                <option value="auto" {{ old('validation_type', 'auto') === 'auto' ? 'selected' : '' }}>Automatique</option>
                <option value="manuelle" {{ old('validation_type') === 'manuelle' ? 'selected' : '' }}>Manuelle</option>
              </select>
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Paiement</label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm">
                  <input type="radio" name="est_payant" value="0" {{ old('est_payant', '0') == '0' ? 'checked' : '' }}>
                  <span>Gratuit</span>
                </label>
                <label class="flex items-center gap-2 text-sm">
                  <input type="radio" name="est_payant" value="1" {{ old('est_payant') == '1' ? 'checked' : '' }}>
                  <span>Payant</span>
                </label>
              </div>
            </div>
            <div id="prix-block" class="hidden">
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Prix</label>
              <input type="number" step="0.01" min="0" name="prix" value="{{ old('prix') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm" placeholder="Ex. 5000">
              <label class="mt-3 mb-2 block text-sm font-semibold text-[#1F2E26]">Moyen de paiement</label>
              <select name="moyen_paiement" class="w-full rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] px-4 py-3 text-sm">
                <option value="">Sélectionner</option>
                <option value="wave">Wave</option>
                <option value="orange_money">Orange Money</option>
              </select>
            </div>
          </div>
        </div>
      </section>

      <section class="rounded-[24px] border border-[#D4CABC] bg-[#F8F2E6] p-5">
        <h2 class="text-lg font-semibold text-[#1F2E26]">Organisateur et contact</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Organisé par</label>
            <input type="text" value="{{ $user->prenom }} {{ $user->nom }}" disabled class="w-full rounded-2xl border border-[#D4CABC] bg-white px-4 py-3 text-sm">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Contact</label>
            <input type="text" name="contact" value="{{ old('contact') }}" class="w-full rounded-2xl border border-[#D4CABC] bg-white px-4 py-3 text-sm" placeholder="Téléphone ou email">
          </div>
        </div>
      </section>

      <div class="flex justify-end">
        <button type="submit" class="rounded-full bg-[#1F2E26] px-6 py-3 text-sm font-semibold text-[#F7F1E3] hover:bg-[#5E6E52]">Créer l'événement</button>
      </div>
    </form>
  </div>
</div>

<script>
  const visibiliteInputs = document.querySelectorAll('input[name="visibilite"]');
  const restrictionBlock = document.getElementById('restriction-block');
  const modeInputs = document.querySelectorAll('input[name="mode"]');
  const lieuBlock = document.getElementById('lieu-block');
  const lienBlock = document.getElementById('lien-block');
  const estPayantInputs = document.querySelectorAll('input[name="est_payant"]');
  const prixBlock = document.getElementById('prix-block');
  const allDayCheckbox = document.getElementById('all-day');
  const dateDebutInput = document.querySelector('input[name="date_debut"]');
  const dateFinInput = document.querySelector('input[name="date_fin"]');

  function toggleRestriction() {
    const isPrivate = Array.from(visibiliteInputs).some(input => input.checked && input.value === 'prive');
    restrictionBlock.classList.toggle('hidden', !isPrivate);
  }

  function toggleMode() {
    const isOnline = Array.from(modeInputs).some(input => input.checked && input.value === 'en_ligne');
    lieuBlock.classList.toggle('hidden', isOnline);
    lienBlock.classList.toggle('hidden', !isOnline);
  }

  function togglePricing() {
    const isPaying = Array.from(estPayantInputs).some(input => input.checked && input.value === '1');
    prixBlock.classList.toggle('hidden', !isPaying);
  }

  function toggleAllDay() {
    if (allDayCheckbox.checked) {
      dateFinInput.value = dateDebutInput.value;
    }
  }

  visibiliteInputs.forEach(input => input.addEventListener('change', toggleRestriction));
  modeInputs.forEach(input => input.addEventListener('change', toggleMode));
  estPayantInputs.forEach(input => input.addEventListener('change', togglePricing));
  allDayCheckbox.addEventListener('change', toggleAllDay);

  toggleRestriction();
  toggleMode();
  togglePricing();
</script>
@endsection
