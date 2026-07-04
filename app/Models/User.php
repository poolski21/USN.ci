<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\FriendRequest;
use App\Models\Group;
use App\Models\Post;
use App\Models\SocialMessage;
use App\Models\SocialNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'prenom', 'nom', 'matricule', 'handle', 'email', 'password', 'universite', 'filiere', 'niveau', 'avatar', 'cover_photo', 'bio', 'cv_url', 'cv_path', 'github', 'private_documents', 'private_friends', 'private_projects', 'last_seen'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function socialNotifications()
    {
        return $this->hasMany(SocialNotification::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(SocialMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(SocialMessage::class, 'receiver_id');
    }

    public function friendRequestsSent()
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    public function friendRequestsReceived()
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    public function friends()
    {
        $sent = FriendRequest::where('sender_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('receiver_id');

        $received = FriendRequest::where('receiver_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('sender_id');

        return self::whereIn('id', $sent->merge($received));
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')->withTimestamps();
    }

    public function adminGroups()
    {
        return $this->hasMany(Group::class, 'admin_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'private_documents' => 'boolean',
            'private_friends' => 'boolean',
            'private_projects' => 'boolean',
            'last_seen' => 'datetime',
        ];
    }
}
