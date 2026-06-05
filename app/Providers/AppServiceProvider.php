<?php

namespace App\Providers;

use App\Services\SiteConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $timezone = SiteConfigService::timezone();

            if ($timezone && in_array($timezone, timezone_identifiers_list(), true)) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        } catch (\Exception) {
            // 数据库表尚未初始化（如测试 SQLite :memory: 环境）时静默跳过
        }
    }
}
