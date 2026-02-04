<x-admin-layouts.admin>
    @slot('title', '编辑分类')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.categories.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; 返回列表
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">编辑分类</h1>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="name">
                    名称 <span class="text-red-500">*</span>
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name', $category->name) }}"
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
                    value="{{ old('slug', $category->slug) }}"
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
                >{{ old('description', $category->description) }}</textarea>
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
                    value="{{ old('sort_order', $category->sort_order) }}"
                    min="0"
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon_type">
                    图标类型
                </label>
                <select
                    class="shadow border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon_type"
                    name="icon_type"
                    onchange="toggleIconFields()"
                >
                    <option value="emoji" {{ old('icon_type', $category->icon_type ?? 'emoji') === 'emoji' ? 'selected' : '' }}>Emoji / Unicode</option>
                    <option value="font-awesome" {{ old('icon_type', $category->icon_type) === 'font-awesome' ? 'selected' : '' }}>Font Awesome</option>
                    <option value="image" {{ old('icon_type', $category->icon_type) === 'image' ? 'selected' : '' }}>网站图片</option>
                </select>
            </div>

            <div class="mb-4" id="icon_emoji_field">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon">
                    图标 (Emoji)
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon"
                    type="text"
                    name="icon"
                    value="{{ old('icon', $category->icon) }}"
                    placeholder="如: 📁"
                >
            </div>

            <div class="mb-4 hidden" id="icon_fa_field">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon_fa">
                    Font Awesome 类名
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon_fa"
                    type="text"
                    name="icon"
                    value="{{ old('icon', $category->icon) }}"
                    placeholder="如: fa-solid fa-home"
                >
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">请输入完整的 Font Awesome 类名（如：fa-solid fa-home）</p>
            </div>

            <div class="mb-4 hidden" id="icon_url_field">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon_url">
                    图标链接
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon_url"
                    type="url"
                    name="icon_url"
                    value="{{ old('icon_url', $category->icon_url) }}"
                    placeholder="https://example.com/icon.png"
                >
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        class="form-checkbox h-4 w-4 text-blue-600"
                        {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                    >
                    <span class="ml-2 text-gray-700 dark:text-gray-300">启用</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="submit"
                >
                    更新
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleIconFields() {
            const iconType = document.getElementById('icon_type').value;
            document.getElementById('icon_emoji_field').classList.toggle('hidden', iconType !== 'emoji');
            document.getElementById('icon_fa_field').classList.toggle('hidden', iconType !== 'font-awesome');
            document.getElementById('icon_url_field').classList.toggle('hidden', iconType !== 'image');
        }
        document.addEventListener('DOMContentLoaded', function() {
            toggleIconFields();
            document.getElementById('icon_type').addEventListener('change', toggleIconFields);
        });
    </script>
</x-admin-layouts.admin>
