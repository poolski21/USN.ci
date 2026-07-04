<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SocialMessage extends Model
{
    protected $table = 'social_messages';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }
}
