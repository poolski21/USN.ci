<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvenementLike extends Model
{
    use HasFactory;

    protected $table = 'evenement_likes';

    protected $fillable = [
        'evenement_id',
        'user_id',
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'evenement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
