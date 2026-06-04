<x-admin-layouts.admin>
    @slot('title', '编辑 Head 注入')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.custom-headers.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; 返回列表
            </a>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">编辑 Head 注入</h1>

        <form method="POST" action="{{ route('admin.custom-headers.update', $customHeader) }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
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
                    value="{{ old('name', $customHeader->name) }}"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="content">
                    HTML 内容 <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                    输入需要注入到前端页面 &lt;head&gt; 中的 HTML 代码（&lt;/head&gt; 之前）。可用于添加统计代码、自定义 CSS/JS 等。
                </p>
                <textarea
                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 font-mono text-sm"
                    id="content"
                    name="content"
                    rows="8"
                    required
                >{{ old('content', $customHeader->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                    value="{{ old('sort_order', $customHeader->sort_order) }}"
                    min="0"
                >
            </div>

            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        id="is_active_toggle"
                        {{ old('is_active', $customHeader->is_active) ? 'checked' : '' }}
                        class="sr-only"
                    >
                    <div id="toggle_slot" class="toggle-slot relative w-11 h-6 {{ old('is_active', $customHeader->is_active) ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }} rounded-full cursor-pointer transition-colors">
                        <div id="toggle_handle" class="toggle-handle absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 ease-in-out {{ old('is_active', $customHeader->is_active) ? 'translate-x-5' : '' }}"></div>
                    </div>
                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">启用</span>
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
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('is_active_toggle');
            const slot = document.getElementById('toggle_slot');
            const handle = document.getElementById('toggle_handle');

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    slot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                    slot.classList.add('bg-blue-600');
                    handle.classList.add('translate-x-5');
                } else {
                    slot.classList.remove('bg-blue-600');
                    slot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                    handle.classList.remove('translate-x-5');
                }
            });
        });
    </script>
</x-admin-layouts.admin>
