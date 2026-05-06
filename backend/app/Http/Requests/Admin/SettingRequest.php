<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_url' => ['required', 'url', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'auto_publish_score_threshold' => ['required', 'integer', 'min:0', 'max:100'],
            'default_fetch_interval' => ['required', 'integer', 'min:15', 'max:10080'],
            'homepage_featured_category' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ];
    }
}
