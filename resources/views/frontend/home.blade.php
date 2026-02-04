<x-frontend-layouts.app>
    <div class="mb-8">
        <form action="{{ route('home') }}" method="GET" class="flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="搜索链接..."
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
            >
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                搜索
            </button>
        </form>
    </div>

    @if($query)
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                搜索结果: "{{ $query }}"
            </h2>

            @if($links->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400">没有找到相关结果</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($links as $link)
                        <a
                            href="{{ route('click', $link) }}"
                            class="block bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-lg transition"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">
                                    @if(($link->icon_type ?? 'emoji') === 'emoji')
                                        {{ $link->icon ?? '🔗' }}
                                    @elseif($link->icon_type === 'font-awesome')
                                        <i class="{{ $link->icon }}"></i>
                                    @elseif($link->icon_type === 'image')
                                        <img src="{{ $link->icon_url }}" alt="icon" class="w-6 h-6 object-contain">
                                    @endif
                                </span>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $link->title }}
                                    </h3>
                                    @if($link->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                            {{ $link->description }}
                                        </p>
                                    @endif
                                    @if($link->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($link->tags as $tag)
                                                <span class="px-2 py-0.5 text-xs rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="space-y-8">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span>
                            @if(($category->icon_type ?? 'emoji') === 'emoji')
                                {{ $category->icon ?? '📁' }}
                            @elseif($category->icon_type === 'font-awesome')
                                <i class="{{ $category->icon }}"></i>
                            @elseif($category->icon_type === 'image')
                                <img src="{{ $category->icon_url }}" alt="icon" class="w-6 h-6 object-contain">
                            @endif
                        </span>
                        {{ $category->name }}
                        @if($category->description)
                            <span class="text-sm font-normal text-gray-600 dark:text-gray-400">
                                - {{ $category->description }}
                            </span>
                        @endif
                    </h2>

                    @if($category->activeLinks->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">暂无链接</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($category->activeLinks as $link)
                                <a
                                    href="{{ route('click', $link) }}"
                                    class="block bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-600 transition"
                                >
                                    <div class="flex items-start gap-3">
                                        <span class="text-xl">
                                            @if(($link->icon_type ?? 'emoji') === 'emoji')
                                                {{ $link->icon ?? '🔗' }}
                                            @elseif($link->icon_type === 'font-awesome')
                                                <i class="{{ $link->icon }}"></i>
                                            @elseif($link->icon_type === 'image')
                                                <img src="{{ $link->icon_url }}" alt="icon" class="w-5 h-5 object-contain">
                                            @endif
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                                {{ $link->title }}
                                            </h3>
                                            @if($link->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                                    {{ $link->description }}
                                                </p>
                                            @endif
                                            @if($link->tags->isNotEmpty())
                                                <div class="flex flex-wrap gap-1 mt-2">
                                                    @foreach($link->tags as $tag)
                                                        <span class="px-2 py-0.5 text-xs rounded-full" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                            {{ $tag->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if($categories->isEmpty())
                <div class="text-center py-12">
                    <p class="text-gray-600 dark:text-gray-400">暂无分类</p>
                </div>
            @endif
        </div>
    @endif
</x-frontend-layouts.app>
