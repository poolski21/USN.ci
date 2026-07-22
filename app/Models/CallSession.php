<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'room',
        'caller_id',
        'callee_id',
        'type',
        'status',
        'offer',
        'answer',
        'caller_candidates',
        'callee_candidates',
    ];

    protected $casts = [
        'offer' => 'array',
        'answer' => 'array',
        'caller_candidates' => 'array',
        'callee_candidates' => 'array',
    ];

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function callee()
    {
        return $this->belongsTo(User::class, 'callee_id');
    }
}
