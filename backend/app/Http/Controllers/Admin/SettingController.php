<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function edit()
    {
        $keys = $this->keys();
        $settings = [];

        foreach ($keys as $key) {
            $settings[$key] = Setting::getValue($key, '');
        }

        return view('admin.settings.edit', compact('keys', 'settings'));
    }

    public function update(SettingRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            Setting::setValue($key, $value);
        }

        return back()->with('status', 'Settings saved.');
    }

    private function keys(): array
    {
        return [
            'site_name',
            'site_url',
            'site_description',
            'auto_publish_score_threshold',
            'default_fetch_interval',
            'homepage_featured_category',
            'contact_email',
            'seo_title',
            'seo_description',
        ];
    }
}
