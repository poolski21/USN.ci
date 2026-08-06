<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\OfficialPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OfficialPageController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        abort_unless($user->is_certified, 403);

        return view('official_pages.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->is_certified, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'avatar_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['user_id'] = $user->id;
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $data['is_active'] = true;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('official_pages', 'public');
        }

        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')->store('official_pages', 'public');
        }

        $page = OfficialPage::create($data);

        return redirect()->route('official_pages.show', $page->slug)->with('status', 'Page officielle créée.');
    }

    public function show(string $slug)
    {
        $page = OfficialPage::where('slug', $slug)->firstOrFail();

        return view('official_pages.show', compact('page'));
    }

    public function edit(string $slug)
    {
        $page = OfficialPage::where('slug', $slug)->firstOrFail();
        abort_unless(Auth::id() === $page->user_id, 403);

        return view('official_pages.edit', compact('page'));
    }

    public function update(Request $request, string $slug)
    {
        $page = OfficialPage::where('slug', $slug)->firstOrFail();
        abort_unless(Auth::id() === $page->user_id, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'avatar_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('official_pages', 'public');
        }

        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')->store('official_pages', 'public');
        }

        $page->update($data);

        return redirect()->route('official_pages.show', $page->slug)->with('status', 'Page officielle mise à jour.');
    }
}
