<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Une photo de profil est requise.',
            'avatar.image' => 'Le fichier de profil doit être une image.',
            'avatar.mimes' => 'La photo de profil doit être au format JPG, JPEG, PNG ou WEBP.',
            'avatar.max' => 'La photo de profil ne peut pas dépasser 4 Mo.',
        ];
    }
}
