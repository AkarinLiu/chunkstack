<x-admin-layouts.admin>
    @slot('title', '仪表盘')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="text-2xl mb-2">📁</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $stats['categories'] }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                分类总数
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="text-2xl mb-2">🔗</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $stats['links'] }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                链接总数
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="text-2xl mb-2">🏷️</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $stats['tags'] }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                标签总数
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <div class="text-2xl mb-2">👆</div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $stats['total_clicks'] }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                总点击数
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">欢迎使用导航站管理后台</h2>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
            您可以在这里管理所有的分类、链接和标签。
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.categories.index') }}" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition">
                <div class="font-semibold text-blue-900 dark:text-blue-100">管理分类</div>
                <div class="text-sm text-blue-700 dark:text-blue-300 mt-1">添加、编辑和删除分类</div>
            </a>
            <a href="{{ route('admin.links.index') }}" class="bg-green-50 dark:bg-green-900 p-4 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition">
                <div class="font-semibold text-green-900 dark:text-green-100">管理链接</div>
                <div class="text-sm text-green-700 dark:text-green-300 mt-1">添加、编辑和删除链接</div>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition">
                <div class="font-semibold text-purple-900 dark:text-purple-100">管理标签</div>
                <div class="text-sm text-purple-700 dark:text-purple-300 mt-1">添加、编辑和删除标签</div>
            </a>
        </div>
    </div>
</x-admin-layouts.admin>
