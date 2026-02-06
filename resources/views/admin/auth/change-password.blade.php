<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">修改密码</h1>

            <form method="POST" action="{{ route('admin.password.change.submit') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="current_password">
                        当前密码
                    </label>
                    <input
                        class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 @error('current_password') border-red-500 @enderror"
                        id="current_password"
                        type="password"
                        name="current_password"
                        required
                        autofocus
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
                        修改密码
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        取消
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layouts.admin>
