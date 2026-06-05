<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-xl">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">恢复码</h1>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="text-center mb-6">
                    <i class="fa-solid fa-triangle-exclamation text-5xl text-yellow-500 mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">
                        请立即保存以下恢复码！
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        每个恢复码仅可使用一次。丢失恢复码将导致您在无法使用双重验证时无法登录账户。
                    </p>
                </div>

                @if(session('recovery_codes'))
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="font-mono text-sm space-y-2">
                            @foreach(session('recovery_codes') as $index => $code)
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-500 dark:text-gray-400 w-6 text-right">{{ $index + 1 }}.</span>
                                    <span class="text-gray-900 dark:text-white select-all">{{ $code }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-center">无恢复码可显示。</p>
                @endif

                <div class="flex justify-center">
                    <a
                        href="{{ route('admin.2fa.setup') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                    >
                        我已安全保存，返回设置
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layouts.admin>
