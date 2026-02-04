<x-admin-layouts.admin>
    @slot('title', '创建链接')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.links.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; 返回列表
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">创建链接</h1>

        <form method="POST" action="{{ route('admin.links.store') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="category_id">
                    分类 <span class="text-red-500">*</span>
                </label>
                <select
                    class="shadow border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="category_id"
                    name="category_id"
                    required
                >
                    <option value="">选择分类</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="title">
                    标题 <span class="text-red-500">*</span>
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="url">
                    链接地址 <span class="text-red-500">*</span>
                </label>
                <input
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="url"
                    type="url"
                    name="url"
                    value="{{ old('url') }}"
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
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="icon_type">
                    图标类型
                </label>
                <select
                    class="shadow border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"
                    id="icon_type"
                    name="icon_type"
                    onchange="toggleIconFields()"
                >
                    <option value="emoji" {{ old('icon_type', 'emoji') === 'emoji' ? 'selected' : '' }}>Emoji / Unicode</option>
                    <option value="font-awesome" {{ old('icon_type') === 'font-awesome' ? 'selected' : '' }}>Font Awesome</option>
                    <option value="image" {{ old('icon_type') === 'image' ? 'selected' : '' }}>网站图片</option>
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
                    value="{{ old('icon') }}"
                    placeholder="如: 🔗"
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
                    value="{{ old('icon') }}"
                    placeholder="如: fa-brands fa-github"
                >
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">请输入完整的 Font Awesome 类名（如：fa-brands fa-github）</p>
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
                    value="{{ old('icon_url') }}"
                    placeholder="https://example.com/icon.png"
                >
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
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    标签
                </label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                class="form-checkbox h-4 w-4 text-blue-600"
                                {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 px-2 py-1 rounded" style="background-color: {{ $tag->color }}20;">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
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
