<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialPageSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'official_page_id',
        'user_id',
    ];

    public function page()
    {
        return $this->belongsTo(OfficialPage::class, 'official_page_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
