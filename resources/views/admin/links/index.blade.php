<x-admin-layouts.admin>
    @slot('title', '链接列表')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">链接列表</h1>
        <a href="{{ route('admin.links.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            创建链接
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        标题
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        分类
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        标签
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        点击
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
                @foreach($links as $link)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">
                                    @if(($link->icon_type ?? 'emoji') === 'emoji')
                                        {{ $link->icon ?? '🔗' }}
                                    @elseif($link->icon_type === 'font-awesome')
                                        <i class="{{ $link->icon }} dark:text-white"></i>
                                    @elseif($link->icon_type === 'image')
                                        <img src="{{ $link->icon_url }}" alt="icon" class="w-6 h-6 object-contain">
                                    @endif
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $link->title }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                        {{ $link->url }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $link->category?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($link->tags as $tag)
                                    <span class="px-2 py-1 text-sm rounded-full text-white" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $link->click_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($link->is_active)
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
                            <a href="{{ route('admin.links.edit', $link) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                编辑
                            </a>
                            <form action="{{ route('admin.links.destroy', $link) }}" method="POST" class="inline">
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

        @if($links->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-600 dark:text-gray-400">暂无链接</p>
            </div>
        @endif
    </div>

    {{ $links->links('pagination::bootstrap-4') }}
</x-admin-layouts.admin>
