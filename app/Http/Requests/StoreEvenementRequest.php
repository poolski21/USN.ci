<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvenementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'categorie' => ['required', 'string', 'in:Conférence,Soirée/Fête,Sport,Culturel,Académique,Association étudiante,Networking,Autre'],
            'image_couverture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'visibilite' => ['required', 'in:public,prive'],
            'restriction_type' => ['nullable', 'in:groupe,filiere,invites'],
            'restriction_id' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'mode' => ['required', 'in:presentiel,en_ligne'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'lien_ligne' => ['nullable', 'url', 'max:255'],
            'places_max' => ['nullable', 'integer', 'min:1'],
            'inscription_requise' => ['nullable', 'boolean'],
            'validation_type' => ['nullable', 'in:auto,manuelle'],
            'est_payant' => ['nullable', 'boolean'],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'contact' => ['nullable', 'string', 'max:255'],
        ];
    }
}
