<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        $source = $this->route('source');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('sources', 'slug')->ignore($source)],
            'official_url' => ['required', 'url', 'max:2048'],
            'feed_url' => ['nullable', 'string', 'max:2048'],
            'source_type' => ['required', Rule::in(['rss', 'api', 'webpage'])],
            'category_id' => ['nullable', 'exists:categories,id'],
            'trust_level' => ['required', Rule::in(['official', 'government', 'research', 'open_source', 'company'])],
            'is_high_priority' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'fetch_frequency_minutes' => ['required', 'integer', 'min:15', 'max:10080'],
        ];
    }
}
