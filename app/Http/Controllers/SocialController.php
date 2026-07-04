<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\SocialMessage;
use App\Models\SocialNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function sendFriendRequest(string $handle)
    {
        $sender = Auth::user();
        $receiver = User::where('handle', $handle)->orWhere('id', $handle)->firstOrFail();

        if ($sender->id === $receiver->id) {
            return back()->with('status', 'Impossible de vous ajouter vous-même.');
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
                return back()->with('status', 'Vous êtes déjà amis.');
            }

            if ($existing->status === 'pending') {
                if ($existing->sender_id === $sender->id) {
                    return back()->with('status', 'Demande déjà envoyée.');
                }

                return back()->with('status', 'Vous avez déjà reçu une invitation de cet utilisateur.');
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

        return back()->with('status', 'Demande d\'ami envoyée.');
    }

    public function acceptFriendRequest(int $id)
    {
        $request = FriendRequest::find($id);

        if (! $request) {
            return back()->with('status', 'Demande introuvable ou déjà traitée.');
        }

        if ($request->receiver_id !== Auth::id() || $request->status !== 'pending') {
            return back()->with('status', 'Cette demande ne peut pas être acceptée.');
        }

        $request->update(['status' => 'accepted']);

        SocialNotification::create([
            'user_id' => $request->sender_id,
            'type' => 'friend_request_accepted',
            'data' => [
                'receiver_id' => $request->receiver_id,
                'receiver_name' => Auth::user()->name,
                'receiver_handle' => Auth::user()->handle,
            ],
        ]);

        return back()->with('status', 'Invitation acceptée.');
    }

    public function declineFriendRequest(int $id)
    {
        $request = FriendRequest::find($id);

        if (! $request) {
            return back()->with('status', 'Demande introuvable ou déjà traitée.');
        }

        if ($request->receiver_id !== Auth::id() || $request->status !== 'pending') {
            return back()->with('status', 'Cette demande ne peut pas être refusée.');
        }

        $request->update(['status' => 'declined']);

        return back()->with('status', 'Invitation refusée.');
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

        $threads = $conversations->map(function ($messages, $otherId) {
            return [
                'friend' => User::find($otherId),
                'last' => $messages->first(),
            ];
        })->filter();

        $selected = null;
        $messages = collect();

        if ($request->filled('with')) {
            $selected = User::where('handle', $request->query('with'))->first();

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
        $selected = User::where('handle', $handle)->orWhere('id', $handle)->firstOrFail();

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
                })->map(function ($messages, $otherId) {
                    return [
                        'friend' => User::find($otherId),
                        'last' => $messages->first(),
                    ];
                })->filter(),
            'selected' => $selected,
            'messages' => $messages,
        ]);
    }

    public function markMessagesRead(Request $request, string $handle)
    {
        $user = Auth::user();
        $other = User::where('handle', $handle)->orWhere('id', $handle)->firstOrFail();

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

    public function sendMessage(Request $request, string $handle)
    {
        $request->validate([
            'body' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('attachment')) {
            return back()->with('status', 'Vous devez écrire un message ou joindre un fichier.');
        }

        $sender = Auth::user();
        $receiver = User::where('handle', $handle)->orWhere('id', $handle)->firstOrFail();

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

    public function storePost(Request $request)
    {
        $request->validate([
            'contenu' => 'nullable|string|max:2000',
            'media' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,webm,pdf,doc,docx',
            'visibilite' => 'nullable|in:public,amis,prive',
            'group_id' => 'nullable|integer|exists:groups,id',
        ]);

        $post = new Post();
        $post->user_id = Auth::id();
        $post->contenu = $request->input('contenu');
        $post->visibilite = $request->input('visibilite', 'public');
        $post->group_id = $request->input('group_id');

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $post->media_path = $file->store('posts', 'public');
            $post->media_type = $file->getClientMimeType();
            $post->media_name = $file->getClientOriginalName();
        }

        $post->save();

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

    public function editPost(Request $request, int $id)
    {
        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            return back()->with('status', 'Vous ne pouvez modifier que vos propres publications.');
        }

        $request->validate([
            'contenu' => 'nullable|string|max:2000',
            'media' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,mov,webm,pdf,doc,docx',
        ]);

        $post->contenu = $request->input('contenu', $post->contenu);

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

    public function storeGroup(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'visibilite' => 'required|in:public,prive',
            'max_members' => 'nullable|integer|min:2|max:500',
        ], [
            'nom.required' => 'Le nom du groupe est obligatoire.',
            'visibilite.required' => 'La visibilité du groupe est obligatoire.',
            'visibilite.in' => 'La visibilité doit être publique ou privée.',
            'max_members.integer' => 'Le nombre maximum de membres doit être un nombre.',
            'max_members.min' => 'Le groupe doit accepter au moins 2 membres.',
            'max_members.max' => 'Le groupe ne peut pas dépasser 500 membres.',
        ]);

        $group = Group::create([
            'nom' => $request->input('nom'),
            'slug' => Str::slug($request->input('nom')) . '-' . uniqid(),
            'admin_id' => Auth::id(),
            'description' => $request->input('description'),
            'visibilite' => $request->input('visibilite', 'public'),
            'max_members' => $request->input('max_members'),
        ]);

        $group->membres()->attach(Auth::id());

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
    public function sendGroupMessage(Request $request, string $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();

        if (!$group->membres->contains(Auth::id())) {
            return back()->with('status', 'Vous devez être membre du groupe pour envoyer des messages.');
        }

        $request->validate([
            'contenu' => 'required|string|max:2000',
            'fichier' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip',
        ]);

        $message = new \App\Models\GroupMessage();
        $message->group_id = $group->id;
        $message->user_id = Auth::id();
        $message->contenu = $request->input('contenu');

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

    public function notifications()
    {
        $notifications = Auth::user()->socialNotifications()->latest()->get();

        return view('notifications', compact('notifications'));
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
