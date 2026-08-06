@extends('layouts.app')

@section('title', 'Modifier la page officielle')

@section('content')
<div class="mx-auto max-w-4xl py-8">
  <div class="rounded-3xl border border-[#D4CABC] bg-white p-8 shadow-sm">
    <h1 class="text-3xl font-bold text-[#1F2E26]">Modifier la page officielle</h1>
    <p class="mt-3 text-sm text-[#5E6E52]">Mettez à jour le titre, la description et les images de votre page.</p>

    <form action="{{ route('official_pages.update', $page->slug) }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-6">
      @csrf
      @method('PATCH')

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label for="title" class="mb-2 block text-sm font-medium text-[#1F2E26]">Titre de la page</label>
          <input id="title" name="title" type="text" value="{{ old('title', $page->title) }}" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none" required>
        </div>
        <div>
          <label for="description" class="mb-2 block text-sm font-medium text-[#1F2E26]">Description</label>
          <textarea id="description" name="description" rows="4" class="w-full rounded-2xl border border-[#D4CABC] bg-[#FBF8F1] px-4 py-3 text-sm text-[#1F2E26] focus:border-[#1F2E26] focus:outline-none">{{ old('description', $page->description) }}</textarea>
        </div>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label for="cover_image" class="mb-2 block text-sm font-medium text-[#1F2E26]">Image de couverture</label>
          <input id="cover_image" name="cover_image" type="file" accept="image/*" class="w-full text-sm text-[#1F2E26]">
        </div>
        <div>
          <label for="avatar_image" class="mb-2 block text-sm font-medium text-[#1F2E26]">Logo / avatar</label>
          <input id="avatar_image" name="avatar_image" type="file" accept="image/*" class="w-full text-sm text-[#1F2E26]">
        </div>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('official_pages.show', $page->slug) }}" class="rounded-2xl border border-[#D4CABC] px-4 py-3 text-sm font-medium text-[#1F2E26] hover:bg-[#F8F2E6]">Annuler</a>
        <button type="submit" class="rounded-2xl bg-[#1F2E26] px-5 py-3 text-sm font-semibold text-white hover:bg-[#15201A]">Enregistrer les modifications</button>
      </div>
    </form>
  </div>
</div>
@endsection
