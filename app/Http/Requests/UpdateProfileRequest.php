<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'bio' => ['nullable', 'string', 'max:2000'],
            'github' => ['nullable', 'url', 'max:255'],
            'cv_url' => ['nullable', 'url', 'max:255'],
            'filiere' => ['nullable', 'string', 'max:255'],
            'niveau' => ['nullable', 'string', 'max:255'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'private_documents' => ['sometimes', 'boolean'],
            'private_friends' => ['sometimes', 'boolean'],
            'private_projects' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'github.url' => 'L’URL GitHub doit être valide.',
            'cv_url.url' => 'L’URL du CV doit être valide.',
            'cv.mimes' => 'Le CV doit être un fichier PDF, DOC ou DOCX.',
            'cv.max' => 'Le CV ne peut pas dépasser 5 Mo.',
        ];
    }
}
