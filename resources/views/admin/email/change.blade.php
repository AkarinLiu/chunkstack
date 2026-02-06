<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">更改邮箱地址</h1>

            @if($user->pending_email)
                <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200 px-4 py-3 rounded mb-4">
                    <p class="font-bold">有待验证的邮箱地址</p>
                    <p class="text-sm">您已提交更改邮箱请求，新邮箱地址：<strong>{{ $user->pending_email }}</strong></p>
                    <p class="text-sm mt-2">请查收验证邮件并点击链接完成验证。如果未收到邮件，可以点击下方的按钮重新发送。</p>
                </div>

                <div class="flex flex-col gap-3 mb-4">
                    <form method="POST" action="{{ route('admin.email.resend') }}">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            重新发送验证邮件
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.email.cancel') }}">
                        @csrf
                        <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            取消更改
                        </button>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('admin.email.change.submit') }}" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            当前邮箱地址
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-500 dark:text-gray-400 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700"
                            type="text"
                            value="{{ $user->email }}"
                            disabled
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">
                            新邮箱地址
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 @error('email') border-red-500 @enderror"
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="current_password">
                            当前密码
                        </label>
                        <input
                            class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 @error('current_password') border-red-500 @enderror"
                            id="current_password"
                            type="password"
                            name="current_password"
                            required
                        >
                        @error('current_password')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            发送验证邮件
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            取消
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-admin-layouts.admin>
