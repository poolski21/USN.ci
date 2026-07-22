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
        'attachment_public_id',
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
        if ($this->attachment_public_id) {
            $cloudinary = app(\App\Services\CloudinaryService::class);

            if ($this->attachment_type && str_starts_with($this->attachment_type, 'image/')) {
                return $cloudinary->url($this->attachment_public_id, 1200, 1200);
            }

            if ($this->attachment_type && str_starts_with($this->attachment_type, 'video/')) {
                return $cloudinary->videoUrl($this->attachment_public_id, 1200, 1200);
            }

            return $cloudinary->fileUrl($this->attachment_public_id);
        }

        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }
}
