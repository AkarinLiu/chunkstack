<?php

namespace App\Services;

use App\Models\SiteSetting;

class SiteConfigService
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return SiteSetting::getValue($key, $default);
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        SiteSetting::setValue($key, $value, $type, $description);
    }

    public static function siteName(): string
    {
        return self::get('site.name', config('app.name', '导航站'));
    }

    public static function siteDescription(): string
    {
        return self::get('site.description', '一个简洁的导航网站');
    }

    public static function siteUrl(): string
    {
        return self::get('site.url', config('app.url', 'http://localhost'));
    }

    public static function enableSitemap(): bool
    {
        return self::get('site.enable_sitemap', true);
    }

    public static function sitemapFrequency(): string
    {
        return self::get('site.sitemap_frequency', 'daily');
    }

    public static function sitemapPriority(): float
    {
        return self::get('site.sitemap_priority', 0.8);
    }

    public static function initializeDefaults(): void
    {
        $defaults = [
            'site.name' => [
                'value' => config('app.name', '导航站'),
                'type' => 'string',
                'description' => '网站名称',
            ],
            'site.description' => [
                'value' => '一个简洁的导航网站',
                'type' => 'string',
                'description' => '网站描述',
            ],
            'site.url' => [
                'value' => config('app.url', 'http://localhost'),
                'type' => 'string',
                'description' => '网站URL',
            ],
            'site.enable_sitemap' => [
                'value' => true,
                'type' => 'boolean',
                'description' => '是否启用站点地图',
            ],
            'site.sitemap_frequency' => [
                'value' => 'daily',
                'type' => 'string',
                'description' => '站点地图更新频率',
            ],
            'site.sitemap_priority' => [
                'value' => 0.8,
                'type' => 'float',
                'description' => '站点地图优先级',
            ],
        ];

        foreach ($defaults as $key => $config) {
            if (! SiteSetting::where('key', $key)->exists()) {
                SiteSetting::create([
                    'key' => $key,
                    'value' => $config['value'],
                    'type' => $config['type'],
                    'description' => $config['description'],
                ]);
            }
        }
    }
}
