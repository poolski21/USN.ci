<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $postId = $this->route('id');
        $post = Post::find($postId);

        return $this->user() !== null && $post && $post->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'contenu' => ['nullable', 'string', 'max:2000'],
            'media' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,gif,mp4,mov,webm,pdf,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'contenu.max' => 'Le message ne peut pas dépasser 2000 caractères.',
            'media.mimes' => 'Le média doit être une image, une vidéo ou un document pris en charge.',
            'media.max' => 'Le média ne peut pas dépasser 10 Mo.',
        ];
    }
}
