<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', '管理后台') - {{ config('app.name', '导航站') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
        <nav class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ config('app.name', '导航站') }}
                        </a>
                        <div class="hidden md:flex items-center gap-6">
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                仪表盘
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                分类
                            </a>
                            <a href="{{ route('admin.links.index') }}" class="{{ request()->routeIs('admin.links.*') ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                链接
                            </a>
                            <a href="{{ route('admin.tags.index') }}" class="{{ request()->routeIs('admin.tags.*') ? 'text-blue-600' : 'text-gray-700 dark:text-gray-300' }}">
                                标签
                            </a>
                        </div>
                    </div>
                    @auth
                    <div class="flex items-center">
                        <div class="relative" id="user-menu">
                            <button id="user-menu-button" class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none">
                                <span>{{ Auth::user()->name }}</span>
                                <svg id="user-menu-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50">
                                <a href="{{ route('admin.email.change') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    更改邮箱
                                </a>
                                <a href="{{ route('admin.password.change') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    修改密码
                                </a>
                                <form action="{{ route('admin.logout') }}" method="POST" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        退出登录
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const menuButton = document.getElementById('user-menu-button');
                const menuDropdown = document.getElementById('user-menu-dropdown');
                const menuIcon = document.getElementById('user-menu-icon');

                if (menuButton && menuDropdown) {
                    menuButton.addEventListener('click', function(e) {
                        e.stopPropagation();
                        menuDropdown.classList.toggle('hidden');
                        menuIcon.classList.toggle('rotate-180');
                    });

                    document.addEventListener('click', function() {
                        menuDropdown.classList.add('hidden');
                        menuIcon.classList.remove('rotate-180');
                    });
                }
            });
        </script>
    </body>
</html>
