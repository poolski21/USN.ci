<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un groupe — USN</title>
  <link rel="stylesheet" href="https://cdn.tailwindcss.com">
</head>
<body class="bg-[#E8E0CE] text-[#221E18] font-sans">
  <div class="min-h-screen px-4 py-8 md:py-10">
    <div class="mx-auto max-w-2xl space-y-6">
      <a href="{{ route('profil.show') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#5E6E52] transition hover:text-[#1F2E26]">← Retour au profil</a>

      <!-- En-tête -->
      <div class="overflow-hidden rounded-[32px] border border-[#D4CABC] bg-white shadow-[0_25px_70px_-30px_rgba(31,46,38,0.45)]">
        <div class="bg-gradient-to-r from-[#1F2E26] via-[#23392E] to-[#2E4A3A] px-6 py-8 text-white md:px-8 md:py-10">
          <h1 class="text-4xl font-bold tracking-tight">Créer un groupe</h1>
          <p class="mt-3 text-sm leading-6 text-[#D4CABC]">
            Lancez un nouveau groupe pour rassembler vos collègues autour de vos projets.
          </p>
        </div>

        <!-- Formulaire -->
        <div class="p-6 md:p-8">
          @if(session('status'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
              {{ session('status') }}
            </div>
          @endif

          @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
              <ul class="space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                  <li>• {{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('groupes.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nom du groupe -->
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Nom du groupe *</label>
              <input 
                type="text" 
                name="nom" 
                value="{{ old('nom') }}" 
                required 
                placeholder="Ex: Projet USN 2026"
                class="w-full rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40 @error('nom') border-red-500 @enderror"
              >
              @error('nom')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Description -->
            <div>
              <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Description</label>
              <textarea 
                name="description" 
                rows="4" 
                placeholder="Décrivez l'objectif et la mission du groupe..."
                class="w-full rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40 @error('description') border-red-500 @enderror"
              >{{ old('description') }}</textarea>
              @error('description')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Grille : Visibilité et Max Membres -->
            <div class="grid gap-6 sm:grid-cols-2">
              <!-- Visibilité -->
              <div>
                <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Visibilité *</label>
                <select 
                  name="visibilite" 
                  required
                  class="w-full rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40 @error('visibilite') border-red-500 @enderror"
                >
                  <option value="">-- Sélectionnez --</option>
                  <option value="public" {{ old('visibilite') === 'public' ? 'selected' : '' }}>🌐 Public</option>
                  <option value="prive" {{ old('visibilite') === 'prive' ? 'selected' : '' }}>🔒 Privé</option>
                </select>
                @error('visibilite')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <!-- Nombre max de membres -->
              <div>
                <label class="mb-2 block text-sm font-semibold text-[#1F2E26]">Nombre max de membres</label>
                <input 
                  type="number" 
                  name="max_members" 
                  value="{{ old('max_members') }}"
                  min="2"
                  max="500"
                  placeholder="Laissez vide pour illimité"
                  class="w-full rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#E2A33B]/40 @error('max_members') border-red-500 @enderror"
                >
                @error('max_members')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <!-- Infos -->
            <div class="rounded-2xl border border-[#D4CABC] bg-[#F8F2E6] p-4">
              <p class="text-xs font-semibold text-[#5E6E52] uppercase tracking-[0.1em]">📌 À savoir</p>
              <ul class="mt-2 space-y-1 text-xs text-[#2F3A30]">
                <li>• Vous serez automatiquement administrateur du groupe</li>
                <li>• Les groupes publics sont visibles par tous les utilisateurs</li>
                <li>• Les groupes privés nécessitent une invitation pour rejoindre</li>
              </ul>
            </div>

            <!-- Boutons -->
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
              <a href="{{ route('profil.show') }}" class="inline-flex items-center justify-center rounded-2xl border border-[#D4CABC] bg-white px-6 py-3 text-sm font-semibold text-[#1F2E26] transition hover:bg-[#F8F2E6]">
                Annuler
              </a>
              <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-[#1F2E26] to-[#2E4A3A] px-8 py-3 text-sm font-semibold text-white transition hover:shadow-lg">
                ✨ Créer le groupe
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
