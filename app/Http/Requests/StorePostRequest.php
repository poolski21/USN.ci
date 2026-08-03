<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'contenu' => ['nullable', 'string', 'max:2000', 'required_without:media'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,mp4,mov,webm,pdf,doc,docx', 'max:10240'],
            'visibilite' => ['nullable', 'in:public,amis,prive'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id', new \App\Rules\UserIsMemberOfGroup($this->user())],
        ];
    }

    public function messages(): array
    {
        return [
            'contenu.required_without' => 'Vous devez écrire quelque chose ou ajouter un média.',
            'media.mimes' => 'Le média doit être une image, une vidéo ou un document pris en charge.',
            'media.max' => 'Le média ne peut pas dépasser 10 Mo.',
            'group_id' => 'Vous devez être membre du groupe pour y poster.',
        ];
    }
}
