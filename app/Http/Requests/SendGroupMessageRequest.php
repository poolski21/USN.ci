<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendGroupMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'contenu' => ['required', 'string', 'max:2000'],
            'fichier' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'contenu.required' => 'Le contenu du message est requis.',
            'contenu.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            'fichier.mimes' => 'Le fichier joint doit être aux formats JPG, JPEG, PNG, GIF, PDF, DOC, DOCX, TXT ou ZIP.',
            'fichier.max' => 'Le fichier joint ne peut pas dépasser 10 Mo.',
        ];
    }
}
