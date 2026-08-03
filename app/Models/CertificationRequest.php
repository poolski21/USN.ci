<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference',
        'university',
        'package',
        'amount',
        'payment_status',
        'status',
        'provider_transaction_id',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
