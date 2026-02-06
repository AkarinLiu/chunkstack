<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">重置密码</h1>

            <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                请设置您的新密码。
            </p>

            <form method="POST" action="{{ route('admin.password.reset.submit') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                        邮箱地址
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 bg-gray-100 dark:bg-gray-600"
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $email) }}"
                        readonly
                        required
                    >
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password">
                        新密码
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 @error('password') border-red-500 @enderror"
                        id="password"
                        type="password"
                        name="password"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password_confirmation">
                        确认新密码
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700"
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                    >
                </div>

                <div class="flex items-center justify-between">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit"
                    >
                        重置密码
                    </button>
                    <a href="{{ route('admin.login') }}" class="text-blue-600 hover:text-blue-800">
                        返回登录
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layouts.admin>
