<x-admin-layouts.admin>
    @slot('title', '创建分类')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; 返回列表
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">创建分类</h1>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="name">
                    名称 <span class="text-red-500">*</span>
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="slug">
                    URL 别名 <span class="text-red-500">*</span>
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ old('slug') }}"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="description">
                    描述
                </label>
                <textarea
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="description"
                    name="description"
                    rows="3"
                >{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="sort_order">
                    排序
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="sort_order"
                    type="number"
                    name="sort_order"
                    value="{{ old('sort_order', 0) }}"
                    min="0"
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon">
                    图标
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon"
                    type="text"
                    name="icon"
                    value="{{ old('icon') }}"
                    placeholder="如: 📁"
                >
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        class="form-checkbox h-4 w-4 text-blue-600"
                        checked
                    >
                    <span class="ml-2 text-gray-700 dark:text-gray-300">启用</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit"
                >
                    创建
                </button>
            </div>
        </form>
    </div>
</x-admin-layouts.admin>
