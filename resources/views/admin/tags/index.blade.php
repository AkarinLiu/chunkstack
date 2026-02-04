<x-admin-layouts.admin>
    @slot('title', '标签列表')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">标签列表</h1>
        <a href="{{ route('admin.tags.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            创建标签
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        名称
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        URL 别名
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        颜色
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        链接数
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($tags as $tag)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-sm rounded-full text-white" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $tag->slug }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-block w-6 h-6 rounded" style="background-color: {{ $tag->color }}"></span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">{{ $tag->color }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $tag->links_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                编辑
                            </a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('确定要删除吗？')">
                                    删除
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($tags->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400">暂无标签</p>
            </div>
        @endif
    </div>
</x-admin-layouts.admin>
