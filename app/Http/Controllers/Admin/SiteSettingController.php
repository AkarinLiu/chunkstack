<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\SiteConfigService;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->groupBy(function ($item) {
            return explode('.', $item->key)[0];
        });

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site.name' => 'required|string|max:255',
            'site.description' => 'required|string|max:500',
            'site.url' => 'required|url|max:255',
            'site.enable_sitemap' => 'boolean',
            'site.sitemap_frequency' => 'required|in:always,hourly,daily,weekly,monthly,yearly,never',
            'site.sitemap_priority' => 'required|numeric|min:0|max:1',
        ]);

        foreach ($validated as $key => $value) {
            SiteConfigService::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', '站点设置已更新');
    }
}
