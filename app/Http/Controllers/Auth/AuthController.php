<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\Post;
use App\Models\SocialMessage;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
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
    public function connexion(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->filled('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['last_seen' => Carbon::now()]);
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
    public function inscription(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'matricule' => 'required|string|max:50',
            'universite' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $name = trim($request->input('prenom') . ' ' . $request->input('nom'));

        $handle = Str::slug($request->input('prenom') . ' ' . $request->input('nom')); 
        $handle = substr($handle, 0, 40) . '-' . Str::lower($request->input('matricule'));

        $user = User::create([
            'name' => $name,
            'prenom' => $request->input('prenom'),
            'nom' => $request->input('nom'),
            'matricule' => $request->input('matricule'),
            'universite' => $request->input('universite'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'handle' => $handle,
        ]);

        Auth::login($user);

        return redirect()->route('profil.show')->with('status', 'Inscription réussie.');
    }

    public function logout(Request $request)
    {
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
    public function updateProfil(Request $request, $handle = null)
    {
        $request->validate([
            'bio' => 'nullable|string|max:2000',
            'github' => 'nullable|url|max:255',
            'cv_url' => 'nullable|url|max:255',
            'filiere' => 'nullable|string|max:255',
            'niveau' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'private_documents' => 'sometimes|boolean',
            'private_friends' => 'sometimes|boolean',
            'private_projects' => 'sometimes|boolean',
        ]);

        $user = Auth::user();
        $user->bio = $request->input('bio');
        $user->github = $request->input('github');
        $user->cv_url = $request->input('cv_url');
        $user->filiere = $request->input('filiere');
        $user->niveau = $request->input('niveau');

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
    public function updateCover(Request $request, $handle = null)
    {
        $request->validate([
            'cover_photo' => 'required|image|max:4096',
        ]);

        $user = Auth::user();
        $path = $request->file('cover_photo')->store('cover_photos', 'public');
        $user->cover_photo = $path;
        $user->save();

        return back()->with('status', 'Photo de couverture mise à jour.');
    }

    // Update profile avatar
    public function updateAvatar(Request $request, $handle = null)
    {
        $request->validate([
            'avatar' => 'required|image|max:4096',
        ]);

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
