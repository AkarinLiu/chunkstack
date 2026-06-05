<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">双重验证</h1>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @if($hasTotp)
                    <div id="totp-section">
                        <p class="text-gray-600 dark:text-gray-400 text-center mb-6">
                            请输入您身份验证器应用中的 6 位动态码
                        </p>

                        <form method="POST" action="{{ route('admin.2fa.verify') }}" class="mb-6">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="code">
                                    动态验证码
                                </label>
                                <input
                                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 text-center text-2xl tracking-widest @error('code') border-red-500 @enderror"
                                    id="code"
                                    type="text"
                                    name="code"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="6"
                                    placeholder="000000"
                                    required
                                    autofocus
                                    autocomplete="one-time-code"
                                >
                                @error('code')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        name="remember_device"
                                        class="form-checkbox h-4 w-4 text-blue-600"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">信任此设备 30 天</span>
                                </label>
                            </div>

                            <button
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit"
                            >
                                验证
                            </button>
                        </form>
                    </div>
                @endif

                @if($hasTotp && $hasWebauthn)
                    <div class="text-center mb-4">
                        <span class="text-gray-500 dark:text-gray-400">或者</span>
                    </div>
                @endif

                @if($hasWebauthn)
                    <div id="webauthn-section" class="text-center mb-6">
                        <button
                            id="webauthn-button"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="button"
                        >
                            <i class="fa-solid fa-key"></i> 使用安全密钥
                        </button>
                        <p id="webauthn-error" class="text-red-500 text-xs mt-2 hidden"></p>
                    </div>
                @endif

                @if($hasTotp || $hasWebauthn)
                    <div class="text-center">
                        <button
                            id="show-recovery-link"
                            class="text-sm text-blue-600 hover:text-blue-800"
                            type="button"
                        >
                            无法使用验证方式？使用恢复码
                        </button>
                    </div>

                    <div id="recovery-section" class="hidden mt-4">
                        <form method="POST" action="{{ route('admin.2fa.recovery') }}">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="recovery_code">
                                    恢复码
                                </label>
                                <input
                                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 @error('recovery_code') border-red-500 @enderror"
                                    id="recovery_code"
                                    type="text"
                                    name="recovery_code"
                                    placeholder="输入恢复码"
                                    autocomplete="off"
                                >
                                @error('recovery_code')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit"
                            >
                                使用恢复码
                            </button>
                        </form>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const recoveryLink = document.getElementById('show-recovery-link');
                            const recoverySection = document.getElementById('recovery-section');

                            if (recoveryLink && recoverySection) {
                                recoveryLink.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    recoverySection.classList.toggle('hidden');
                                });
                            }
                        });
                    </script>
                @endif
            </div>
        </div>
    </div>

    @if($hasWebauthn)
    <script>
        function arrayBufferToBase64(buffer) {
            const bytes = new Uint8Array(buffer);
            let binary = '';
            for (let i = 0; i < bytes.length; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return btoa(binary);
        }

        function base64urlToUint8Array(base64url) {
            const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
            const padded = base64.padEnd(base64.length + (4 - base64.length % 4) % 4, '=');
            return Uint8Array.from(atob(padded), c => c.charCodeAt(0));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const webauthnButton = document.getElementById('webauthn-button');
            const webauthnError = document.getElementById('webauthn-error');

            if (!webauthnButton) return;

            webauthnButton.addEventListener('click', async function() {
                webauthnButton.disabled = true;
                webauthnButton.textContent = '正在验证...';
                webauthnError.classList.add('hidden');

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]')?.value;

                    const response = await fetch('{{ route("admin.2fa.webauthn.assertion") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('获取挑战失败');
                    }

                    const options = await response.json();

                    options.publicKey.challenge = base64urlToUint8Array(options.publicKey.challenge);

                    if (options.publicKey.allowCredentials) {
                        options.publicKey.allowCredentials.forEach(cred => {
                            cred.id = base64urlToUint8Array(cred.id);
                        });
                    }

                    const assertion = await navigator.credentials.get(options);

                    const verifyResponse = await fetch('{{ route("admin.2fa.webauthn.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            id: assertion.id,
                            rawId: arrayBufferToBase64(assertion.rawId),
                            type: assertion.type,
                            response: {
                                authenticatorData: arrayBufferToBase64(assertion.response.authenticatorData),
                                clientDataJSON: arrayBufferToBase64(assertion.response.clientDataJSON),
                                signature: arrayBufferToBase64(assertion.response.signature),
                                userHandle: assertion.response.userHandle
                                    ? arrayBufferToBase64(assertion.response.userHandle)
                                    : null,
                            },
                        }),
                    });

                    const result = await verifyResponse.json();

                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        throw new Error(result.error || '验证失败');
                    }
                } catch (error) {
                    webauthnError.textContent = error.message || '验证失败，请重试';
                    webauthnError.classList.remove('hidden');
                    webauthnButton.disabled = false;
                    webauthnButton.innerHTML = '<i class="fa-solid fa-key"></i> 使用安全密钥';
                }
            });
        });
    </script>
    @endif
</x-admin-layouts.admin>
