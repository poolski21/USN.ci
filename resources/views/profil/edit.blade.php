{{-- resources/views/profil/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier le profil — USN')

@section('content')
  <div class="mt-6 max-w-3xl mx-auto">
    <h1 class="text-xl font-semibold text-ardoise mb-4">Modifier mon profil</h1>

    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white/90 p-6 rounded-2xl border border-ardoise/10">
      @csrf
      @method('PATCH')

      <div>
        <label class="block text-sm font-medium text-ardoise mb-1">Bio</label>
        <textarea name="bio" rows="4" class="w-full rounded-md border px-3 py-2">{{ old('bio', $user->bio) }}</textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm font-medium text-ardoise mb-1">Filière</label>
          <input type="text" name="filiere" value="{{ old('filiere', $user->filiere) }}" class="w-full rounded-md border px-3 py-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-ardoise mb-1">Niveau</label>
          <input type="text" name="niveau" value="{{ old('niveau', $user->niveau) }}" class="w-full rounded-md border px-3 py-2">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-ardoise mb-1">Lien GitHub</label>
        <input type="url" name="github" value="{{ old('github', $user->github) }}" class="w-full rounded-md border px-3 py-2">
      </div>

      <div class="space-y-2">
        <h3 class="text-sm font-semibold text-ardoise">Confidentialité</h3>
        <p class="text-xs text-gray-500">Choisissez quelles informations rendre privées.</p>

        <div class="flex items-center gap-3 mt-3">
          <label class="flex items-center gap-2">
            <input type="checkbox" name="private_documents" {{ old('private_documents', $user->private_documents) ? 'checked' : '' }}>
            <span class="text-sm">Documents privés</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="checkbox" name="private_friends" {{ old('private_friends', $user->private_friends) ? 'checked' : '' }}>
            <span class="text-sm">Liste d'amis privée</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="checkbox" name="private_projects" {{ old('private_projects', $user->private_projects) ? 'checked' : '' }}>
            <span class="text-sm">Projets privés</span>
          </label>
        </div>
      </div>

      <div class="pt-3 border-t">
        <button type="submit" class="px-4 py-2 bg-ardoise text-kraft rounded-lg">Enregistrer</button>
        <a href="{{ route('profil.show') }}" class="ml-3 text-sm text-gray-500">Annuler</a>
      </div>
    </form>
  </div>
@endsection
{{-- resources/views/profil/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Éditer le profil — USN')

@section('content')
  <div class="mb-6 rounded-3xl bg-white/90 border border-ardoise/20 p-5 shadow-sm mt-6">
    <div class="flex items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-ardoise">Modifier votre profil</h1>
        <p class="text-sm text-gray-500 mt-1">Ajoutez votre bio, votre CV ou votre lien GitHub.</p>
      </div>
      <a href="{{ route('profil.show') }}" class="text-sm text-ardoise/80 hover:text-ardoise">Retour au profil</a>
    </div>

    @if(session('status'))
      <div class="mb-4 rounded-2xl bg-ardoise/10 border border-ardoise/20 px-4 py-3 text-sm text-ardoise">
        {{ session('status') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
      @csrf
      @method('PATCH')

      <div class="space-y-2">
        <label class="text-sm font-medium text-ardoise" for="bio">Bio</label>
        <textarea id="bio" name="bio" rows="5" class="w-full rounded-2xl border border-kraft-dark/40 bg-kraft-light px-4 py-3 text-sm text-ardoise focus:border-ardoise focus:outline-none" placeholder="Parlez de vous...">{{ old('bio', $user->bio) }}</textarea>
      </div>

      <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-2">
          <label class="text-sm font-medium text-ardoise" for="filiere">Filière</label>
          <input id="filiere" name="filiere" type="text" value="{{ old('filiere', $user->filiere) }}" placeholder="Informatique" class="w-full rounded-2xl border border-kraft-dark/40 bg-kraft-light px-4 py-3 text-sm text-ardoise focus:border-ardoise focus:outline-none">
        </div>
        <div class="space-y-2">
          <label class="text-sm font-medium text-ardoise" for="niveau">Niveau</label>
          <input id="niveau" name="niveau" type="text" value="{{ old('niveau', $user->niveau) }}" placeholder="L2" class="w-full rounded-2xl border border-kraft-dark/40 bg-kraft-light px-4 py-3 text-sm text-ardoise focus:border-ardoise focus:outline-none">
        </div>
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div class="space-y-2">
          <label class="text-sm font-medium text-ardoise" for="github">Lien GitHub</label>
          <input id="github" name="github" type="url" value="{{ old('github', $user->github) }}" placeholder="https://github.com/votre-nom" class="w-full rounded-2xl border border-kraft-dark/40 bg-kraft-light px-4 py-3 text-sm text-ardoise focus:border-ardoise focus:outline-none">
        </div>
        <div class="space-y-2">
          <label class="text-sm font-medium text-ardoise" for="cv_url">Lien CV</label>
          <input id="cv_url" name="cv_url" type="url" value="{{ old('cv_url', $user->cv_url) }}" placeholder="https://..." class="w-full rounded-2xl border border-kraft-dark/40 bg-kraft-light px-4 py-3 text-sm text-ardoise focus:border-ardoise focus:outline-none">
        </div>
      </div>

      <div class="space-y-2">
        <label class="text-sm font-medium text-ardoise" for="cv">Télécharger un CV</label>
        <input id="cv" name="cv" type="file" accept=".pdf,.doc,.docx" class="block w-full text-sm text-ardoise file:mr-4 file:rounded-full file:border-0 file:bg-ardoise file:px-4 file:py-2 file:text-white file:shadow-sm file:hover:bg-ardoise-dark" />
        @if($user->cv_path)
          <p class="text-xs text-gray-500">CV existant : <a href="{{ asset('storage/'.$user->cv_path) }}" target="_blank" class="text-moutarde hover:underline">Voir le CV</a></p>
        @endif
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('profil.show') }}" class="rounded-2xl border border-ardoise/20 px-4 py-3 text-sm text-ardoise hover:bg-ardoise/5">Annuler</a>
        <button type="submit" class="rounded-2xl bg-ardoise px-5 py-3 text-sm font-semibold text-white hover:bg-ardoise-dark">Enregistrer</button>
      </div>
    </form>
  </div>
@endsection
