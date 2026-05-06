<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $article = $this->route('article');

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($article)],
            'summary' => ['nullable', 'string'],
            'content_excerpt' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'author' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'published', 'rejected', 'ignored'])],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'ai_summary' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
        ];
    }
}
