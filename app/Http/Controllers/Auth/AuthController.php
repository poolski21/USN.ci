<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateCoverPhotoRequest;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\Post;
use App\Models\SocialMessage;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Show the connexion (login) form
    public function showConnexion()
    {
        return view('connexion');
    }

    // Handle connexion (login) POST
    public function connexion(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['last_seen' => Carbon::now()]);
            ActivityLogger::log(Auth::user(), 'user.login', 'Connexion réussie', ['email' => $credentials['email']]);
            return redirect()->route('profil.show');
        }

        return back()->withErrors(['email' => 'Identifiants invalides'])->onlyInput('email');
    }

    // Show the inscription (register) form
    public function showInscription()
    {
        return view('inscription');
    }

    // Handle inscription (register) POST
    public function inscription(RegisterRequest $request)
    {
        $validated = $request->validated();

        $name = trim($validated['prenom'] . ' ' . $validated['nom']);

        $handle = Str::slug($request->input('prenom') . ' ' . $request->input('nom')); 
        $handle = substr($handle, 0, 40) . '-' . Str::lower($request->input('matricule'));

        $user = User::create([
            'name' => $name,
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'matricule' => $validated['matricule'],
            'universite' => $validated['universite'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'handle' => $handle,
        ]);

        Auth::login($user);
        ActivityLogger::log($user, 'user.register', 'Nouvel utilisateur inscrit', ['email' => $user->email]);

        return redirect()->route('profil.show')->with('status', 'Inscription réussie.');
    }

    public function logout(Request $request)
    {
        ActivityLogger::log(Auth::user(), 'user.logout', 'Déconnexion utilisateur');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('connexion')->with('status', 'Vous êtes déconnecté.');
    }

    public function searchFriends(Request $request)
    {
        $q = trim($request->query('q', ''));
        $results = collect();

        if ($q !== '') {
            $results = User::where('id', '!=', Auth::id())
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                          ->orWhere('prenom', 'like', "%{$q}%")
                          ->orWhere('nom', 'like', "%{$q}%")
                          ->orWhere('filiere', 'like', "%{$q}%");
                })
                ->paginate(12)
                ->withQueryString();
        }

        return view('search', compact('results', 'q'));
    }

    // Show the profile edit page
    public function showEditProfil($handle = null)
    {
        $user = Auth::user();
        return view('profil.edit', compact('user'));
    }

    // Update profile content
    public function updateProfil(UpdateProfileRequest $request, $handle = null)
    {
        $validated = $request->validated();

        $user = Auth::user();
        $user->bio = $validated['bio'] ?? null;
        $user->github = $validated['github'] ?? null;
        $user->cv_url = $validated['cv_url'] ?? null;
        $user->filiere = $validated['filiere'] ?? null;
        $user->niveau = $validated['niveau'] ?? null;

        if ($request->hasFile('cv')) {
            $path = $request->file('cv')->store('cvs', 'public');
            $user->cv_path = $path;
        }

        // privacy toggles
        $user->private_documents = $request->boolean('private_documents');
        $user->private_friends = $request->boolean('private_friends');
        $user->private_projects = $request->boolean('private_projects');

        $user->save();

        return redirect()->route('profil.edit')->with('status', 'Profil mis à jour.');
    }

    // Update profile cover photo
    public function updateCover(UpdateCoverPhotoRequest $request, $handle = null)
    {
        $user = Auth::user();
        $path = $request->file('cover_photo')->store('cover_photos', 'public');
        $user->cover_photo = $path;
        $user->save();

        return back()->with('status', 'Photo de couverture mise à jour.');
    }

    // Update profile avatar
    public function updateAvatar(UpdateAvatarRequest $request, $handle = null)
    {
        $user = Auth::user();
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return back()->with('status', 'Photo de profil mise à jour.');
    }

    // Show the authenticated user's profile
    public function showProfil($handle = null)
    {
        $user = $handle
            ? User::where('handle', $handle)->orWhere('id', $handle)->firstOrFail()
            : Auth::user();
        $current = Auth::user();
        $isSelf = $current->id === $user->id;
        $isOnline = $isSelf || ($user->last_seen && $user->last_seen->greaterThan(now()->subMinutes(5)));
        $friendRequest = null;
        $isFriend = false;
        $requestStatus = null;

        if (! $isSelf) {
            $friendRequest = FriendRequest::where(function ($query) use ($current, $user) {
                $query->where('sender_id', $current->id)->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($current, $user) {
                $query->where('sender_id', $user->id)->where('receiver_id', $current->id);
            })->first();

            if ($friendRequest) {
                $requestStatus = $friendRequest->status;
                $isFriend = $requestStatus === 'accepted';
            }
        }

        $stats = [
            'connexions' => $user->friends()->count(),
            'groupes' => $user->groups()->count(),
            'projets' => 0,
            'contributions' => $user->posts()->count(),
            'evenements' => 0,
        ];

        $postsQuery = Post::with(['auteur', 'groupe', 'comments.user'])
            ->where('user_id', $user->id);

        if (! $isSelf && ! $isFriend) {
            // Les publications publiques sont visibles par tous ; les autres sont réservées à l’auteur et à ses amis.
            $postsQuery->where('visibilite', 'public');
        }

        $posts = $postsQuery->latest()->paginate(10);

        $connexions = new LengthAwarePaginator([], 0, 5, 1, ['path' => url()->current()]);
        $pinned = [];
        $projets = collect();
        $groupes = $user->groups()->withCount('membres')->get();
        $evenements = collect();
        // activity section removed
        $documents = SocialMessage::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->whereNotNull('attachment_path')
            ->latest()
            ->get();

        $friends = collect();
        $friendRequests = FriendRequest::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->where('status', 'accepted')
            ->with(['sender', 'receiver'])
            ->get();

        $friends = $friendRequests->map(function (FriendRequest $request) use ($user) {
            $friend = $request->sender_id === $user->id ? $request->receiver : $request->sender;

            return (object) [
                'user' => $friend,
                'friendRequestId' => $request->id,
            ];
        })->filter(function ($relation) {
            return $relation->user !== null;
        });

        $pendingFriendRequests = collect();
        if ($isSelf) {
            $pendingFriendRequests = FriendRequest::where('status', 'pending')
                ->where('receiver_id', $user->id)
                ->with('sender')
                ->latest()
                ->take(5)
                ->get();
        }

        $friendSuggestions = collect();
        if ($isSelf) {
            $friendSuggestions = User::query()
                ->where('id', '!=', $user->id)
                ->whereDoesntHave('friendRequestsSent', function ($query) use ($user) {
                    $query->where('receiver_id', $user->id);
                })
                ->whereDoesntHave('friendRequestsReceived', function ($query) use ($user) {
                    $query->where('sender_id', $user->id);
                })
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        return view('profil', compact(
            'user',
            'stats',
            'posts',
            'connexions',
            'pinned',
            'projets',
            'groupes',
            'evenements',
            'documents',
            'friends',
            'pendingFriendRequests',
            'friendSuggestions',
            'isSelf',
            'isFriend',
            'requestStatus',
            'friendRequest',
            'isOnline'
        ));
    }
}
