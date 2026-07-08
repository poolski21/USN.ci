<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $table = 'evenements';

    protected $fillable = [
        'titre',
        'description',
        'categorie',
        'image_couverture',
        'visibilite',
        'restriction_type',
        'restriction_id',
        'date_debut',
        'date_fin',
        'mode',
        'lieu',
        'lien_ligne',
        'places_max',
        'inscription_requise',
        'validation_type',
        'est_payant',
        'prix',
        'organisateur_id',
        'organisateur_type',
        'contact',
        'likes_count',
        'comments_count',
        'shares_count',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'places_max' => 'integer',
        'inscription_requise' => 'boolean',
        'est_payant' => 'boolean',
    ];

    public function organisateur()
    {
        return $this->morphTo();
    }

    public function likes()
    {
        return $this->hasMany(EvenementLike::class, 'evenement_id');
    }

    public function comments()
    {
        return $this->hasMany(EvenementComment::class, 'evenement_id')->latest();
    }

    public function likedByUser(?int $userId = null): bool
    {
        if ($userId === null) {
            $userId = auth()->id();
        }

        return $userId !== null && $this->likes()->where('user_id', $userId)->exists();
    }
}
