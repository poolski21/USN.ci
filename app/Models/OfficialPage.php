<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'avatar_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(OfficialPageSubscription::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'official_page_subscriptions', 'official_page_id', 'user_id')->withTimestamps();
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        return asset('images/default-cover.png');
    }

    public function getAvatarImageUrlAttribute(): string
    {
        if ($this->avatar_image) {
            return asset('storage/' . $this->avatar_image);
        }

        return asset('images/default-avatar.png');
    }
}
