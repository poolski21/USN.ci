<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\OfficialPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficialPageSubscriptionController extends Controller
{
    public function toggle(Request $request, string $slug)
    {
        $page = OfficialPage::query()->where('slug', '=', $slug)->firstOrFail();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($page->user_id === $user->id) {
            abort(403);
        }

        if ($user->isSubscribedToOfficialPage($page)) {
            $page->subscribers()->detach($user->id);
            return back()->with('status', 'Abonnement annulé.');
        }

        $page->subscribers()->attach($user->id);
        return back()->with('status', 'Vous êtes maintenant abonné à cette page officielle.');
    }
}
