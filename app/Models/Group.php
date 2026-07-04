<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'admin_id',
        'description',
        'avatar',
        'visibilite',
        'max_members',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'group_id')->orderByDesc('created_at');
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_id')->orderByDesc('created_at');
    }
}
