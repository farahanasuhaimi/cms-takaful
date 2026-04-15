<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function api()
    {
        return view('settings.api', [
            'apiKey'  => Setting::get('deepseek_api_key'),
            'model'   => Setting::get('deepseek_model', 'deepseek-chat'),
            'baseUrl' => Setting::get('deepseek_base_url', 'https://api.deepseek.com'),
        ]);
    }

    public function updateApi(Request $request)
    {
        $validated = $request->validate([
            'api_key'  => 'nullable|string|max:255',
            'model'    => 'required|string|max:100',
            'base_url' => 'required|url|max:255',
        ]);

        Setting::set('deepseek_api_key',  $validated['api_key'] ?? null);
        Setting::set('deepseek_model',    $validated['model']);
        Setting::set('deepseek_base_url', $validated['base_url']);

        return back()->with('success', 'API settings saved.');
    }
}
