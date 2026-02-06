<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">邮箱地址确认</h1>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-6">
                    <div class="flex items-center justify-center mb-4">
                        <svg class="w-16 h-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-4">
                        请确认您的邮箱地址
                    </h2>

                    <p class="text-gray-600 dark:text-gray-400 text-center mb-4">
                        您的邮箱地址 <strong>{{ auth()->user()->email }}</strong> 已经超过 180 天未确认。
                    </p>

                    <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                        为确保账号安全，请确认该邮箱地址是否仍在正常使用。我们会发送一封确认邮件到您的邮箱。
                    </p>

                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.email.confirmation.send') }}">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                发送确认邮件
                            </button>
                        </form>

                        <div class="text-center">
                            <span class="text-gray-500 dark:text-gray-400">或者</span>
                        </div>

                        <a href="{{ route('admin.email.change') }}" class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                            没有收到邮件，更改邮箱地址
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-admin-layouts.admin>
