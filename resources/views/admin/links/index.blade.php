<x-admin-layouts.admin>
    @slot('title', '链接列表')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">链接列表</h1>
        <a href="{{ route('admin.links.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            创建链接
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.links.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">搜索</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="标题或 URL..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">分类</label>
                <select name="category"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">全部分类</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">标签</label>
                <select name="tag"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">全部标签</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">状态</label>
                <select name="is_active"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">全部</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>启用</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>禁用</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    筛选
                </button>
                <a href="{{ route('admin.links.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    重置
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <a href="{{ route('admin.links.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'title', 'direction' => request('sort') === 'title' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-100 flex items-center gap-1">
                            标题
                            @if(request('sort') === 'title')
                                <i class="fa-solid fa-sort-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300 dark:text-gray-500"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        分类
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        标签
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <a href="{{ route('admin.links.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'click_count', 'direction' => request('sort') === 'click_count' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-100 flex items-center gap-1">
                            点击
                            @if(request('sort') === 'click_count')
                                <i class="fa-solid fa-sort-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300 dark:text-gray-500"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        <a href="{{ route('admin.links.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'page_view_count', 'direction' => request('sort') === 'page_view_count' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-100 flex items-center gap-1">
                            浏览
                            @if(request('sort') === 'page_view_count')
                                <i class="fa-solid fa-sort-{{ request('direction') === 'desc' ? 'down' : 'up' }}"></i>
                            @else
                                <i class="fa-solid fa-sort text-gray-300 dark:text-gray-500"></i>
                            @endif
                        </a>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                            {{ $link->page_view_count }}
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
