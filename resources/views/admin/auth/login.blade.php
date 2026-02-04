<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">管理员登录</h1>

            <form method="POST" action="{{ route('admin.login.submit') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                        邮箱
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700"
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password">
                        密码
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700"
                        id="password"
                        type="password"
                        name="password"
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            class="form-checkbox h-4 w-4 text-blue-600"
                        >
                        <span class="ml-2 text-gray-700 dark:text-gray-300">记住我</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit"
                    >
                        登录
                    </button>
                </div>
            </form>

            @if(!\App\Models\User::exists())
                <p class="text-center text-gray-600 dark:text-gray-400 mt-4">
                    首次使用?
                    <a href="{{ route('admin.register') }}" class="text-blue-600 hover:text-blue-800">
                        注册管理员
                    </a>
                </p>
            @endif
        </div>
    </div>
</x-admin-layouts.admin>
