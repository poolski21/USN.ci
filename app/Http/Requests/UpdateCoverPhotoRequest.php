<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoverPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'cover_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover_photo.required' => 'Une image de couverture est requise.',
            'cover_photo.image' => 'Le fichier de couverture doit être une image.',
            'cover_photo.mimes' => 'L’image de couverture doit être au format JPG, JPEG, PNG ou WEBP.',
            'cover_photo.max' => 'L’image de couverture ne peut pas dépasser 4 Mo.',
        ];
    }
}
