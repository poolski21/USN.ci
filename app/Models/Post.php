<?php

namespace App\Models;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'contenu',
        'media_path',
        'media_public_id',
        'media_type',
        'media_name',
        'visibilite',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function groupe()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function likedByUser(?int $userId = null): bool
    {
        if ($userId === null) {
            $userId = auth()->id();
        }

        return $userId !== null && $this->likes()->where('user_id', $userId)->exists();
    }

    public function getMediaUrlAttribute(): ?string
    {
        if ($this->media_public_id) {
            $cloudinary = app(\App\Services\CloudinaryService::class);

            if ($this->media_type && str_starts_with($this->media_type, 'image/')) {
                return $cloudinary->url($this->media_public_id, 1200, 1200);
            }

            if ($this->media_type && str_starts_with($this->media_type, 'video/')) {
                return $cloudinary->videoUrl($this->media_public_id, 1200, 1200);
            }

            return $cloudinary->fileUrl($this->media_public_id);
        }

        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }
}
