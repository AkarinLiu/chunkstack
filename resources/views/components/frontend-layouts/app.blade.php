<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ App\Services\SiteConfigService::siteName() }}</title>
        <meta name="description" content="{{ App\Services\SiteConfigService::siteDescription() }}">
        
        @if(App\Services\SiteConfigService::enableSitemap())
            <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">
        @endif
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
        <nav class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ App\Services\SiteConfigService::siteName() }}
                        </a>
                    </div>
                    <div class="flex items-center">
                        @auth
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                管理后台
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                登录
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </main>

        <footer class="bg-white dark:bg-gray-800 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                 <p class="text-center text-gray-600 dark:text-gray-400">
                    &copy; {{ date('Y') }} {{ App\Services\SiteConfigService::siteName() }}. All rights reserved.
                </p>
            </div>
        </footer>
    </body>
</html>
