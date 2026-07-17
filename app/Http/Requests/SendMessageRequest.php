<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt,zip', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.mimes' => 'Le fichier joint doit être au format image, PDF, DOC, DOCX, TXT ou ZIP.',
            'attachment.max' => 'La pièce jointe ne peut pas dépasser 10 Mo.',
        ];
    }
}
