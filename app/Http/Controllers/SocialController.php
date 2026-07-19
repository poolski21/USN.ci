<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Http\Requests\SendGroupMessageRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\SocialMessage;
use App\Models\SocialNotification;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Trouve un utilisateur par son handle, ou par son id si $value est numérique.
     * Évite les erreurs PostgreSQL "invalid input syntax for type bigint"
     * quand on compare un handle non-numérique à la colonne id.
     */
    private function findUserByHandle(string $value)
    {
        return User::where('handle', $value)
            ->when(is_numeric($value), fn ($q) => $q->orWhere('id', $value))
            ->firstOrFail();
    }

    public function sendFriendRequest(Request $request, string $handle)
    {
        $sender = Auth::user();
        $receiver = $this->findUserByHandle($handle);
        if ($sender->id === $receiver->id) {
            return $this->friendRequestResponse($request, 'error', 'Impossible de vous ajouter vous-même.');
        }

        $existing = FriendRequest::where(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id);
        })->orWhere(function ($query) use ($sender, $receiver) {
            $query->where('sender_id', $receiver->id)
                ->where('receiver_id', $sender->id);
        })->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return $this->friendRequestResponse($request, 'accepted', 'Vous êtes déjà amis.');
            }

            if ($existing->status === 'pending') {
                if ($existing->sender_id === $sender->id) {
                    return $this->friendRequestResponse($request, 'pending', 'Demande déjà envoyée.');
                }

                return $this->friendRequestResponse($request, 'pending', 'Vous avez déjà reçu une invitation de cet utilisateur.');
            }

            if ($existing->status === 'declined') {
                $existing->update(['sender_id' => $sender->id, 'receiver_id' => $receiver->id, 'status' => 'pending']);
                $friendRequest = $existing;
            }
        } else {
            $friendRequest = FriendRequest::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'status' => 'pending',
            ]);
        }

        SocialNotification::create([
            'user_id' => $receiver->id,
            'type' => 'friend_request',
            'data' => [
                'friend_request_id' => $friendRequest->id,
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'sender_handle' => $sender->handle,
            ],
        ]);

        ActivityLogger::log($sender, 'friend_request.sent', 'Demande d’ami envoyée', ['receiver_id' => $receiver->id, 'request_id' => $friendRequest->id]);

        return $this->friendRequestResponse($request, 'pending', 'Demande d\'ami envoyée.');
    }

    public function acceptFriendRequest(Request $httpRequest, int $id)
    {
        $friendRequest = FriendRequest::find($id);

        if (! $friendRequest) {
            return $this->friendRequestResponse($httpRequest, 'error', 'Demande introuvable ou déjà traitée.');
        }

        if ($friendRequest->receiver_id !== Auth::id() || $friendRequest->status !== 'pending') {
            return $this->friendRequestResponse($httpRequest, 'error', 'Cette demande ne peut pas être acceptée.');
        }

        $friendRequest->update(['status' => 'accepted']);

        SocialNotification::create([
            'user_id' => $friendRequest->sender_id,
            'type' => 'friend_request_accepted',
            'data' => [
                'receiver_id' => $friendRequest->receiver_id,
                'receiver_name' => Auth::user()->name,
                'receiver_handle' => Auth::user()->handle,
            ],
        ]);

        ActivityLogger::log(Auth::user(), 'friend_request.accepted', 'Demande d’ami acceptée', ['request_id' => $friendRequest->id, 'sender_id' => $friendRequest->sender_id]);

        return $this->friendRequestResponse($httpRequest, 'accepted', 'Invitation acceptée.');
    }

    public function declineFriendRequest(Request $httpRequest, int $id)
    {
        $friendRequest = FriendRequest::find($id);

        if (! $friendRequest) {
            return $this->friendRequestResponse($httpRequest, 'error', 'Demande introuvable ou déjà traitée.');
        }

        if ($friendRequest->receiver_id !== Auth::id() || $friendRequest->status !== 'pending') {
            return $this->friendRequestResponse($httpRequest, 'error', 'Cette demande ne peut pas être refusée.');
        }

        $friendRequest->update(['status' => 'declined']);
        ActivityLogger::log(Auth::user(), 'friend_request.declined', 'Demande d’ami refusée', ['request_id' => $friendRequest->id, 'sender_id' => $friendRequest->sender_id]);

        return $this->friendRequestResponse($httpRequest, 'declined', 'Invitation refusée.');
    }

    private function friendRequestResponse(Request $request, string $state, string $message)
    {
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'state' => $state,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function messages(Request $request)
    {
        $user = Auth::user();

        $conversations = SocialMessage::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest('created_at')
            ->get()
            ->groupBy(function (SocialMessage $message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            });

        $threads = $conversations->map(function ($messages, $otherId) use ($user) {
            return [
                'friend' => User::find($otherId),
                'last' => $messages->first(),
                'unreadCount' => $messages->where('receiver_id', $user->id)->whereNull('read_at')->count(),
            ];
        })->filter();

        $selected = null;
        $messages = collect();

        if ($request->filled('with')) {
            $selected = User::where('handle', $request->query('with'))
                ->when(is_numeric($request->query('with')), fn ($query) => $query->orWhere('id', $request->query('with')))
                ->first();

            if ($selected) {
                $messages = SocialMessage::where(function ($query) use ($user, $selected) {
                    $query->where('sender_id', $user->id)->where('receiver_id', $selected->id);
                })->orWhere(function ($query) use ($user, $selected) {
                    $query->where('sender_id', $selected->id)->where('receiver_id', $user->id);
                })->oldest()->get();
            }
        }

        return view('messages', compact('threads', 'selected', 'messages'));
    }

    public function conversation(string $handle)
    {
        $user = Auth::user();
        $selected = $this->findUserByHandle($handle);

        // Mark incoming messages from the selected user as read for the current user
        SocialMessage::where('sender_id', $selected->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = SocialMessage::where(function ($query) use ($user, $selected) {
            $query->where('sender_id', $user->id)->where('receiver_id', $selected->id);
        })->orWhere(function ($query) use ($user, $selected) {
            $query->where('sender_id', $selected->id)->where('receiver_id', $user->id);
        })->oldest()->get();

        return view('messages', [
            'threads' => SocialMessage::where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->latest('created_at')
                ->get()
                ->groupBy(function (SocialMessage $message) use ($user) {
                    return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
                })->map(function ($messages, $otherId) use ($user) {
                    return [
                        'friend' => User::find($otherId),
                        'last' => $messages->first(),
                        'unreadCount' => $messages->where('receiver_id', $user->id)->whereNull('read_at')->count(),
                    ];
                })->filter(),
            'selected' => $selected,
            'messages' => $messages,
        ]);
    }

    public function markMessagesRead(Request $request, string $handle)
    {
        $user = Auth::user();
        $other = $this->findUserByHandle($handle);

        SocialMessage::where('sender_id', $other->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            $unread = SocialMessage::where('receiver_id', $user->id)->whereNull('read_at')->count();
            return response()->json(['success' => true, 'unreadMessages' => $unread]);
        }

        return back();
    }

    public function sendMessage(SendMessageRequest $request, string $handle)
    {
        $data = $request->validated();

        if (empty(trim($data['body'] ?? '')) && ! $request->hasFile('attachment')) {
            return back()->withErrors(['body' => 'Vous devez écrire un message ou joindre un fichier.'])->withInput();
        }

        $sender = Auth::user();
        $receiver = $this->findUserByHandle($handle);

        if ($sender->id !== $receiver->id) {
            $isFriend = FriendRequest::where(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $sender->id)->where('receiver_id', $receiver->id);
            })->orWhere(function ($query) use ($sender, $receiver) {
                $query->where('sender_id', $receiver->id)->where('receiver_id', $sender->id);
            })->where('status', 'accepted')->exists();

            if (! $isFriend) {
                return back()->with('status', 'Vous devez être amis pour envoyer un message.');
            }
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('messages', 'public');
            $attachmentType = $file->getClientMimeType();
            $attachmentName = $file->getClientOriginalName();
        }

        $message = SocialMessage::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $request->input('body', ''),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);
        ActivityLogger::log($sender, 'message.sent', 'Message envoyé', ['receiver_id' => $receiver->id, 'message_id' => $message->id]);

        SocialNotification::create([
            'user_id' => $receiver->id,
            'type' => 'message_received',
            'data' => [
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'sender_handle' => $sender->handle,
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $message->id,
                'body' => $message->body,
                'attachment' => $message->attachment_path ? [
                    'url' => $message->attachment_url,
                    'name' => $message->attachment_name,
                    'type' => $message->attachment_type,
                ] : null,
                'created_at' => $message->created_at->format('d/m/Y H:i'),
            ], 201);
        }

        return redirect()->route('messages.conversation', ['handle' => $receiver->handle ?? $receiver->id])->with('status', 'Message envoyé.');
    }

    public function storePost(StorePostRequest $request)
    {
        $data = $request->validated();

        $post = new Post();
        $post->user_id = Auth::id();
        $post->contenu = $data['contenu'] ?? null;
        $post->visibilite = $data['visibilite'] ?? 'public';
        $post->group_id = $data['group_id'] ?? null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $post->media_path = $file->store('posts', 'public');
            $post->media_type = $file->getClientMimeType();
            $post->media_name = $file->getClientOriginalName();
        }

        $post->save();
        ActivityLogger::log(Auth::user(), 'post.created', 'Publication créée', ['post_id' => $post->id, 'group_id' => $post->group_id]);

        // Si c'est un post de groupe, ne pas créer de notifications aux amis
        if (!$post->group_id) {
            $friends = Auth::user()->friends()->pluck('id');
            foreach ($friends as $friendId) {
                SocialNotification::create([
                    'user_id' => $friendId,
                    'type' => 'friend_post',
                    'data' => [
                        'post_id' => $post->id,
                        'author_id' => Auth::id(),
                        'author_name' => Auth::user()->name,
                        'author_handle' => Auth::user()->handle,
                        'preview' => Str::limit($post->contenu, 80),
                    ],
                ]);
            }
        }

        return back()->with('status', 'Publication créée.');
    }

    public function editPost(UpdatePostRequest $request, int $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validated();
        $post->contenu = $validated['contenu'] ?? $post->contenu;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $post->media_path = $file->store('posts', 'public');
            $post->media_type = $file->getClientMimeType();
            $post->media_name = $file->getClientOriginalName();
        }

        $post->save();

        return back()->with('status', 'Publication modifiée.');
    }

    public function destroyPost(int $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return back()->with('status', 'Vous ne pouvez supprimer que vos propres publications.');
        }

        $post->delete();

        return back()->with('status', 'Publication supprimée.');
    }

    public function showCreateGroup()
    {
        return view('groupes.create');
    }

    public function storeGroup(StoreGroupRequest $request)
    {
        $data = $request->validated();

        $group = Group::create([
            'nom' => $data['nom'],
            'slug' => Str::slug($data['nom']) . '-' . uniqid(),
            'admin_id' => Auth::id(),
            'description' => $data['description'] ?? null,
            'visibilite' => $data['visibilite'],
            'max_members' => $data['max_members'] ?? null,
        ]);

        $group->membres()->attach(Auth::id());
        ActivityLogger::log(Auth::user(), 'group.created', 'Groupe créé', ['group_id' => $group->id]);

        return redirect()->route('groupes.show', $group->slug)->with('status', 'Le groupe a été créé avec succès.');
    }

    public function showGroup(string $slug)
    {
        $group = Group::where('slug', $slug)->with(['admin', 'membres', 'posts.user', 'messages.user'])->firstOrFail();
        $isMember = $group->membres->contains(Auth::id());
        $isAdmin = $group->admin_id === Auth::id();

        return view('groupes.show', compact('group', 'isMember', 'isAdmin'));
    }

    public function joinGroup(string $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if ($group->membres->contains(Auth::id())) {
            return back()->with('status', 'Vous êtes déjà membre de ce groupe.');
        }

        if (! empty($group->max_members) && $group->membres()->count() >= $group->max_members) {
            return back()->with('status', 'Ce groupe est déjà complet.');
        }

        $group->membres()->attach(Auth::id());

        return back()->with('status', 'Vous avez rejoint le groupe.');
    }

    public function leaveGroup(string $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if ($group->membres->contains(Auth::id())) {
            $group->membres()->detach(Auth::id());
        }

        return back()->with('status', 'Vous avez quitté le groupe.');
    }

    public function addGroupMember(Request $request, string $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if ($group->admin_id !== Auth::id()) {
            return back()->with('status', 'Seul l’administrateur peut ajouter des membres.');
        }

        $userIds = $request->input('user_ids', $request->input('user_id'));
        if (! is_array($userIds)) {
            $userIds = [$userIds];
        }

        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $friendsIds = Auth::user()->friends()->pluck('users.id')->toArray();
        $added = 0;

        foreach ($userIds as $userId) {
            if (! in_array((int) $userId, $friendsIds, true)) {
                continue;
            }

            $user = User::find($userId);
            if (! $user || $group->membres->contains($user->id)) {
                continue;
            }

            if (! empty($group->max_members) && $group->membres()->count() >= $group->max_members) {
                break;
            }

            $group->membres()->attach($user->id);
            $added++;
        }

        return back()->with('status', $added > 0 ? 'Membre(s) ajouté(s) au groupe.' : 'Aucun membre n’a pu être ajouté.');
    }

    public function removeGroupMember(string $slug, int $userId)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if ($group->admin_id !== Auth::id()) {
            return back()->with('status', 'Seul l’administrateur peut retirer un membre.');
        }

        if ($group->membres->contains($userId)) {
            $group->membres()->detach($userId);
        }

        return back()->with('status', 'Membre retiré du groupe.');
    }
    public function sendGroupMessage(SendGroupMessageRequest $request, string $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if (!$group->membres->contains(Auth::id())) {
            return back()->with('status', 'Vous devez être membre du groupe pour envoyer des messages.');
        }

        $data = $request->validated();

        $message = new \App\Models\GroupMessage();
        $message->group_id = $group->id;
        $message->user_id = Auth::id();
        $message->contenu = $data['contenu'];

        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $message->file_path = $file->store('group_messages', 'public');
            $message->file_type = $file->getClientMimeType();
            $message->file_name = $file->getClientOriginalName();
        }

        $message->save();

        return back()->with('status', 'Message envoyé.');
    }

    public function deleteGroupMessage(string $slug, int $messageId)
    {
        $group = Group::where('slug', $slug)->firstOrFail();
        $message = \App\Models\GroupMessage::findOrFail($messageId);

        if ($message->group_id !== $group->id || ($message->user_id !== Auth::id() && $group->admin_id !== Auth::id())) {
            return back()->with('status', 'Vous ne pouvez pas supprimer ce message.');
        }

        $message->delete();

        return back()->with('status', 'Message supprimé.');
    }
    public function removeFriend(int $id)
    {
        $request = FriendRequest::findOrFail($id);

        if ($request->status !== 'accepted' || ! in_array(Auth::id(), [$request->sender_id, $request->receiver_id], true)) {
            return back()->with('status', 'Impossible de supprimer cette relation.');
        }

        $request->delete();

        return back()->with('status', 'Ami supprimé de votre liste.');
    }

    public function likePost(Request $request, int $id)
    {
        $post = Post::findOrFail($id);
        $userId = Auth::id();

        $existingLike = PostLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existingLike) {
            $existingLike->delete();
            $post->decrement('likes_count');
            $message = 'Votre j’aime a été retiré.';
            $liked = false;
        } else {
            PostLike::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $post->increment('likes_count');
            $message = 'Publication aimée.';
            $liked = true;

            if ($post->user_id !== $userId) {
                SocialNotification::create([
                    'user_id' => $post->user_id,
                    'type' => 'post_liked',
                    'data' => [
                        'post_id' => $post->id,
                        'actor_id' => $userId,
                        'actor_name' => Auth::user()->name,
                        'actor_handle' => Auth::user()->handle,
                    ],
                ]);
            }
        }

        $post->refresh();

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likesCount' => (int) $post->likes_count,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function commentPost(Request $request, int $id)
    {
        $request->validate([
            'contenu' => 'required|string|max:1000',
        ]);

        $post = Post::findOrFail($id);
        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'contenu' => $request->input('contenu'),
        ]);

        $post->increment('comments_count');
        ActivityLogger::log(Auth::user(), 'post.comment', 'Commentaire ajouté', ['post_id' => $post->id, 'comment_id' => $comment->id]);

        if ($post->user_id !== Auth::id()) {
            SocialNotification::create([
                'user_id' => $post->user_id,
                'type' => 'post_commented',
                'data' => [
                    'post_id' => $post->id,
                    'comment_id' => $comment->id,
                    'actor_id' => Auth::id(),
                    'actor_name' => Auth::user()->name,
                    'actor_handle' => Auth::user()->handle,
                    'preview' => Str::limit($request->input('contenu'), 80),
                ],
            ]);
        }

        $post->refresh();

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'commentsCount' => (int) $post->comments_count,
                'message' => 'Commentaire ajouté.',
            ]);
        }

        return back()->with('status', 'Commentaire ajouté.');
    }

    public function sharePost(Request $request, int $id)
    {
        $post = Post::findOrFail($id);
        $post->increment('shares_count');
        ActivityLogger::log(Auth::user(), 'post.share', 'Publication partagée', ['post_id' => $post->id]);

        if ($post->user_id !== Auth::id()) {
            SocialNotification::create([
                'user_id' => $post->user_id,
                'type' => 'post_shared',
                'data' => [
                    'post_id' => $post->id,
                    'actor_id' => Auth::id(),
                    'actor_name' => Auth::user()->name,
                    'actor_handle' => Auth::user()->handle,
                ],
            ]);
        }

        $post->refresh();

        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'sharesCount' => (int) $post->shares_count,
                'message' => 'Publication partagée.',
            ]);
        }

        return back()->with('status', 'Publication partagée.');
    }

    public function liveUpdates(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['success' => false], 401);
        }

        $unreadMessages = SocialMessage::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();
        $unreadNotifications = SocialNotification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
        $pendingFriendRequests = FriendRequest::where('status', 'pending')
            ->where('receiver_id', $user->id)
            ->count();

        return response()->json([
            'success' => true,
            'unreadMessages' => $unreadMessages,
            'unreadNotifications' => $unreadNotifications,
            'pendingFriendRequests' => $pendingFriendRequests,
        ]);
    }

    public function notifications()
    {
        $notifications = Auth::user()->socialNotifications()->latest()->get();

        return view('notifications', compact('notifications'));
    }

    public function showFriendRequests()
    {
        $pendingRequests = FriendRequest::where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->with('sender')
            ->latest('created_at')
            ->get();

        return view('friend-requests', compact('pendingRequests'));
    }

    public function markNotificationRead(int $id)
    {
        $notification = Auth::user()->socialNotifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            $unread = Auth::user()->socialNotifications()->whereNull('read_at')->count();
            return response()->json(['success' => true, 'unreadNotifications' => $unread]);
        }

        return back();
    }
}