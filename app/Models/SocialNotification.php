<?php

namespace App\Models;

use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SocialNotification extends Model
{
    protected $table = 'social_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friendRequestId(): ?int
    {
        if (! empty($this->data['friend_request_id'])) {
            return (int) $this->data['friend_request_id'];
        }

        if ($this->type !== 'friend_request' || empty($this->data['sender_id'])) {
            return null;
        }

        $friendRequest = FriendRequest::where('sender_id', $this->data['sender_id'])
            ->where('receiver_id', $this->user_id)
            ->where('status', 'pending')
            ->latest('created_at')
            ->first();

        return $friendRequest?->id;
    }
}
