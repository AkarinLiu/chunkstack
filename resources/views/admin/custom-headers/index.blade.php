<x-admin-layouts.admin>
    @slot('title', 'Head 注入')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Head 注入</h1>
        <a href="{{ route('admin.custom-headers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            创建
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
                        内容预览
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        排序
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        状态
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($headers as $header)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $header->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate block">
                                {{ Str::limit($header->content, 80) }}
                            </code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $header->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($header->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    启用
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    禁用
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.custom-headers.edit', $header) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                编辑
                            </a>
                            <form action="{{ route('admin.custom-headers.destroy', $header) }}" method="POST" class="inline">
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

        @if($headers->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400">暂无 Head 注入</p>
            </div>
        @endif
    </div>
</x-admin-layouts.admin>
