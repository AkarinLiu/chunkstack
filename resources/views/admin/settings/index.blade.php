<x-admin-layouts.admin>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                        站点设置
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        管理网站的基本配置信息
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6 p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">基本设置</h3>

                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                网站名称
                            </label>
                            <input
                                type="text"
                                name="site[name]"
                                id="site_name"
                                value="{{ old('site.name', App\Services\SiteConfigService::siteName()) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                                required
                            >
                            @error('site.name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                网站描述
                            </label>
                            <textarea
                                name="site[description]"
                                id="site_description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                                required
                            >{{ old('site.description', App\Services\SiteConfigService::siteDescription()) }}</textarea>
                            @error('site.description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                网站URL
                            </label>
                            <input
                                type="url"
                                name="site[url]"
                                id="site_url"
                                value="{{ old('site.url', App\Services\SiteConfigService::siteUrl()) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                                required
                            >
                            @error('site.url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">区域设置</h3>

                        <div>
                            <label for="site_timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                站点时区
                            </label>
                            <select
                                name="site[timezone]"
                                id="site_timezone"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                            >
                                @php
                                    $currentTimezone = old('site.timezone', App\Services\SiteConfigService::timezone());
                                    $timezones = [
                                        'UTC' => 'UTC',
                                        'Asia/Shanghai' => '中国标准时间 (UTC+8)',
                                        'Asia/Tokyo' => '日本标准时间 (UTC+9)',
                                        'Asia/Seoul' => '韩国标准时间 (UTC+9)',
                                        'Asia/Singapore' => '新加坡标准时间 (UTC+8)',
                                        'Asia/Hong_Kong' => '香港标准时间 (UTC+8)',
                                        'Asia/Taipei' => '台北标准时间 (UTC+8)',
                                        'Asia/Kolkata' => '印度标准时间 (UTC+5:30)',
                                        'Asia/Dubai' => '迪拜 (UTC+4)',
                                        'Asia/Bangkok' => '曼谷 (UTC+7)',
                                        'Asia/Jakarta' => '雅加达 (UTC+7)',
                                        'Europe/London' => '伦敦 (UTC+0)',
                                        'Europe/Paris' => '巴黎 (UTC+1)',
                                        'Europe/Berlin' => '柏林 (UTC+1)',
                                        'Europe/Moscow' => '莫斯科 (UTC+3)',
                                        'America/New_York' => '纽约 (UTC-5)',
                                        'America/Chicago' => '芝加哥 (UTC-6)',
                                        'America/Denver' => '丹佛 (UTC-7)',
                                        'America/Los_Angeles' => '洛杉矶 (UTC-8)',
                                        'America/Sao_Paulo' => '圣保罗 (UTC-3)',
                                        'Australia/Sydney' => '悉尼 (UTC+10)',
                                        'Pacific/Auckland' => '奥克兰 (UTC+12)',
                                    ];
                                @endphp
                                @foreach($timezones as $value => $label)
                                    <option value="{{ $value }}" {{ $currentTimezone === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('site.timezone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">站点地图设置</h3>

                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="site[enable_sitemap]" value="0">
                                <input
                                    type="checkbox"
                                    name="site[enable_sitemap]"
                                    value="1"
                                    id="sitemap_toggle"
                                    {{ old('site.enable_sitemap', App\Services\SiteConfigService::enableSitemap()) ? 'checked' : '' }}
                                    class="sr-only"
                                >
                                <div id="sitemap_slot" class="toggle-slot relative w-11 h-6 {{ old('site.enable_sitemap', App\Services\SiteConfigService::enableSitemap()) ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }} rounded-full cursor-pointer transition-colors">
                                    <div id="sitemap_handle" class="toggle-handle absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 ease-in-out {{ old('site.enable_sitemap', App\Services\SiteConfigService::enableSitemap()) ? 'translate-x-5' : '' }}"></div>
                                </div>
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">启用站点地图</span>
                            </label>
                        </div>

                        <div>
                            <label for="site_sitemap_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                更新频率
                            </label>
                            <select
                                name="site[sitemap_frequency]"
                                id="site_sitemap_frequency"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                            >
                                <option value="always" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'always' ? 'selected' : '' }}>总是</option>
                                <option value="hourly" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'hourly' ? 'selected' : '' }}>每小时</option>
                                <option value="daily" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'daily' ? 'selected' : '' }}>每天</option>
                                <option value="weekly" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'weekly' ? 'selected' : '' }}>每周</option>
                                <option value="monthly" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'monthly' ? 'selected' : '' }}>每月</option>
                                <option value="yearly" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'yearly' ? 'selected' : '' }}>每年</option>
                                <option value="never" {{ old('site.sitemap_frequency', App\Services\SiteConfigService::sitemapFrequency()) === 'never' ? 'selected' : '' }}>从不</option>
                            </select>
                            @error('site.sitemap_frequency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="site_sitemap_priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                优先级 (0.0 - 1.0)
                            </label>
                            <input
                                type="number"
                                name="site[sitemap_priority]"
                                id="site_sitemap_priority"
                                value="{{ old('site.sitemap_priority', App\Services\SiteConfigService::sitemapPriority()) }}"
                                step="0.1"
                                min="0"
                                max="1"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:text-white sm:text-sm"
                                required
                            >
                            @error('site.sitemap_priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-5">
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                保存设置
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sitemapCheckbox = document.getElementById('sitemap_toggle');
            const sitemapSlot = document.getElementById('sitemap_slot');
            const sitemapHandle = document.getElementById('sitemap_handle');

            if (sitemapCheckbox) {
                sitemapCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        sitemapSlot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                        sitemapSlot.classList.add('bg-blue-600');
                        sitemapHandle.classList.add('translate-x-5');
                    } else {
                        sitemapSlot.classList.remove('bg-blue-600');
                        sitemapSlot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                        sitemapHandle.classList.remove('translate-x-5');
                    }
                });
            }
        });
    </script>
</x-admin-layouts.admin>
