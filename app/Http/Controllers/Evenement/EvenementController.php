<?php

namespace App\Http\Controllers\Evenement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvenementRequest;
use App\Models\Evenement;
use App\Models\EvenementComment;
use App\Models\EvenementLike;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvenementController extends Controller
{
    public function index()
    {
        $evenements = Evenement::with(['comments.user'])->latest()->paginate(10);

        return view('evenements.index', compact('evenements'));
    }

    public function show(Evenement $evenement)
    {
        $evenement->load(['comments.user']);

        return view('evenements.show', compact('evenement'));
    }

    public function create()
    {
        $user = Auth::user();
        $groupes = $user->groups()->orderBy('nom')->get();

        return view('evenements.create', compact('user', 'groupes'));
    }

    public function store(StoreEvenementRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image_couverture')) {
            $data['image_couverture'] = $request->file('image_couverture')->store('evenements', 'public');
        }

        $data['organisateur_id'] = Auth::id();
        $data['organisateur_type'] = 'user';

        if (! empty($data['places_max'])) {
            $data['places_max'] = (int) $data['places_max'];
        } else {
            $data['places_max'] = null;
        }

        if (! empty($data['prix'])) {
            $data['prix'] = (float) $data['prix'];
        } else {
            $data['prix'] = null;
        }

        $data['inscription_requise'] = (bool) $request->boolean('inscription_requise');
        $data['est_payant'] = (bool) $request->boolean('est_payant');

        Evenement::create($data);

        return redirect()->route('evenements.index')
            ->with('status', 'Événement créé avec succès.');
    }

    public function like(Request $request, Evenement $evenement)
    {
        $userId = Auth::id();

        $existing = EvenementLike::where('evenement_id', $evenement->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $evenement->decrement('likes_count');
            $liked = false;
        } else {
            EvenementLike::create([
                'evenement_id' => $evenement->id,
                'user_id' => $userId,
            ]);
            $evenement->increment('likes_count');
            $liked = true;
        }

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likesCount' => (int) $evenement->likes_count,
            ]);
        }

        return back()->with('status', $liked ? 'Événement liké.' : 'Like retiré.');
    }

    public function comment(Request $request, Evenement $evenement)
    {
        $data = $request->validate([
            'contenu' => ['required', 'string', 'max:1000'],
        ]);

        EvenementComment::create([
            'evenement_id' => $evenement->id,
            'user_id' => Auth::id(),
            'contenu' => $data['contenu'],
        ]);

        $evenement->increment('comments_count');

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'commentsCount' => (int) $evenement->comments_count,
                'message' => 'Commentaire ajouté.',
            ]);
        }

        return back()->with('status', 'Commentaire ajouté.');
    }

    public function share(Request $request, Evenement $evenement)
    {
        $evenement->increment('shares_count');

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'sharesCount' => (int) $evenement->shares_count,
                'message' => 'Événement partagé.',
            ]);
        }

        return back()->with('status', 'Événement partagé.');
    }
}
