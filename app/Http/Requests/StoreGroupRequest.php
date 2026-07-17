<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'visibilite' => ['required', 'in:public,prive'],
            'max_members' => ['nullable', 'integer', 'min:2', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du groupe est obligatoire.',
            'visibilite.required' => 'La visibilité du groupe est obligatoire.',
            'visibilite.in' => 'La visibilité doit être publique ou privée.',
            'max_members.integer' => 'Le nombre maximum de membres doit être un nombre.',
            'max_members.min' => 'Le groupe doit accepter au moins 2 membres.',
            'max_members.max' => 'Le groupe ne peut pas dépasser 500 membres.',
        ];
    }
}
