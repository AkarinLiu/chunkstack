<x-admin-layouts.admin>
    <div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
        <div class="w-full max-w-2xl">
            <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-8">双重验证设置</h1>

            @if(session('recovery_codes'))
                <div class="bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200 px-4 py-3 rounded mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-bold text-lg">恢复码</p>
                        <button id="download-recovery-codes" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 dark:hover:bg-yellow-700 rounded transition-colors">
                            <i class="fa-solid fa-download"></i> 下载恢复码
                        </button>
                    </div>
                    <p class="mb-3">请立即保存以下恢复码。每个恢复码只能使用一次，请妥善保管。</p>
                    <div class="bg-white dark:bg-gray-800 rounded p-3 font-mono text-sm">
                        @foreach(session('recovery_codes') as $code)
                            <div class="py-1">{{ $code }}</div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-sm">恢复码已显示，关闭此页面后将无法再次查看完整恢复码。</p>
                </div>
                <script>
                    document.getElementById('download-recovery-codes').addEventListener('click', function() {
                        const codes = {!! Js::from(implode("\n", session('recovery_codes'))) !!};
                        const blob = new Blob([codes], { type: 'text/plain;charset=utf-8' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'chunkstack-recovery-codes.txt';
                        a.click();
                        URL.revokeObjectURL(url);
                    });
                </script>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @if($isEnabled)
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="fa-solid fa-shield-halved mr-1"></i> 已启用
                            </span>
                        </div>

                        <div class="space-y-4">
                            @if($hasTotp)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">身份验证器应用 (TOTP)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">已绑定</p>
                                    </div>
                                    <i class="fa-solid fa-check text-green-500 text-xl"></i>
                                </div>
                            @endif

                            @foreach($webauthnCredentials as $credential)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            <i class="fa-solid fa-key mr-1"></i>
                                            <span class="credential-name">{{ $credential->name }}</span>
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            注册于 {{ $credential->created_at->format('Y-m-d H:i') }} | 计数器: {{ $credential->counter }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button
                                            class="rename-btn text-blue-600 hover:text-blue-800 text-sm"
                                            data-credential-id="{{ $credential->id }}"
                                            data-credential-name="{{ $credential->name }}"
                                            type="button"
                                        >
                                            重命名
                                        </button>
                                        <form method="POST" action="{{ route('admin.2fa.webauthn.credentials.destroy', $credential) }}" class="inline" onsubmit="return confirm('确定删除此安全密钥？')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                删除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            @if(!$hasTotp || count($webauthnCredentials) > 0)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">安全密钥 (FIDO/WebAuthn)</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($webauthnCredentials) }} 个已注册</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">添加更多验证方式或管理现有设置：</p>
                        <div class="flex flex-wrap gap-3">
                            @if(!$hasTotp)
                                <form method="POST" action="{{ route('admin.2fa.enable') }}">
                                    @csrf
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        绑定身份验证器应用
                                    </button>
                                </form>
                            @endif

                            <button id="register-webauthn-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                                注册安全密钥
                            </button>

                            <form method="POST" action="{{ route('admin.2fa.recovery-codes.regenerate') }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    重新生成恢复码
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif(isset($pendingTotp) && $pendingTotp)
                    <div class="text-center mb-6">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            请使用身份验证器应用扫描以下二维码
                        </p>

                        <div class="flex justify-center mb-4">
                            <div class="bg-white p-4 rounded-lg">
                                {!! $qrCodeSvg !!}
                            </div>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            无法扫描？
                            <button id="show-manual-secret" class="text-blue-600 hover:text-blue-800" type="button">
                                点击此处手动输入密钥
                            </button>
                        </p>

                        <div id="manual-secret-section" class="hidden mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">手动输入以下密钥：</p>
                            <div class="bg-gray-100 dark:bg-gray-700 rounded p-3 font-mono text-sm break-all select-all">
                                {{ $secret }}
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.2fa.confirm-enable') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="code">
                                输入身份验证器中的 6 位动态码以确认
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
                            >
                            @error('code')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            确认并启用
                        </button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const manualLink = document.getElementById('show-manual-secret');
                            const manualSection = document.getElementById('manual-secret-section');

                            if (manualLink && manualSection) {
                                manualLink.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    manualSection.classList.toggle('hidden');
                                });
                            }
                        });
                    </script>
                @else
                    <div class="text-center mb-6">
                        <i class="fa-solid fa-shield-halved text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            启用双重验证后，登录时需要提供密码和额外的验证码，有效提升账户安全性。
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            您可以选择使用身份验证器应用 (TOTP) 或安全密钥 (FIDO/WebAuthn)。
                        </p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <form method="POST" action="{{ route('admin.2fa.enable') }}">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline">
                                <i class="fa-solid fa-mobile-screen-button mr-2"></i> 使用身份验证器应用
                            </button>
                        </form>

                        <button id="register-webauthn-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                            <i class="fa-solid fa-key mr-2"></i> 注册安全密钥
                        </button>
                    </div>
                @endif

                @if($isEnabled)
                    <div class="border-t border-gray-200 dark:border-gray-600 mt-6 pt-6">
                        <form method="POST" action="{{ route('admin.2fa.disable') }}" onsubmit="return confirm('确定关闭双重验证？您的账户安全性将降低。')">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="current_password">
                                    输入当前密码以关闭双重验证
                                </label>
                                <input
                                    class="shadow appearance-none border border-gray-300 dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700"
                                    id="current_password"
                                    type="password"
                                    name="current_password"
                                    required
                                >
                            </div>
                            <button
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit"
                            >
                                关闭双重验证
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
            const webauthnBtn = document.getElementById('register-webauthn-btn');
            if (!webauthnBtn) return;

            webauthnBtn.addEventListener('click', async function() {
                if (!window.isSecureContext) {
                    alert('WebAuthn/安全密钥需要 HTTPS 连接。请使用 https:// 访问此网站，或在本地开发环境中使用 localhost。');
                    return;
                }
                webauthnBtn.disabled = true;
                webauthnBtn.textContent = '注册中...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]')?.value;

                    const beginResponse = await fetch('{{ route("admin.2fa.webauthn.register.begin") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    });

                    if (!beginResponse.ok) {
                        throw new Error('获取注册信息失败');
                    }

                    const options = await beginResponse.json();

                    options.publicKey.challenge = base64urlToUint8Array(options.publicKey.challenge);
                    options.publicKey.user.id = base64urlToUint8Array(options.publicKey.user.id);
                    if (options.publicKey.excludeCredentials) {
                        options.publicKey.excludeCredentials.forEach(cred => {
                            cred.id = base64urlToUint8Array(cred.id);
                        });
                    }

                    const credential = await navigator.credentials.create(options);

                    const name = prompt('请为此安全密钥命名:', '我的安全密钥') || '安全密钥';

                    const completeResponse = await fetch('{{ route("admin.2fa.webauthn.register.complete") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            id: credential.id,
                            rawId: arrayBufferToBase64(credential.rawId),
                            type: credential.type,
                            name: name,
                            response: {
                                clientDataJSON: arrayBufferToBase64(credential.response.clientDataJSON),
                                attestationObject: arrayBufferToBase64(credential.response.attestationObject),
                                transports: credential.response.getTransports ? credential.response.getTransports() : [],
                            },
                        }),
                    });

                    if (!completeResponse.ok) {
                        const err = await completeResponse.json();
                        throw new Error(err.error || '注册失败');
                    }

                    await completeResponse.json();

                    location.reload();
                } catch (error) {
                    alert('注册失败: ' + (error.message || '未知错误'));
                    webauthnBtn.disabled = false;
                    webauthnBtn.innerHTML = '<i class="fa-solid fa-key mr-2"></i> 注册安全密钥';
                }
            });

            document.querySelectorAll('.rename-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const credentialId = this.dataset.credentialId;
                    const currentName = this.dataset.credentialName;
                    const newName = prompt('重命名安全密钥:', currentName);
                    if (!newName || newName === currentName) return;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("admin.2fa.webauthn.credentials.update", ["credential" => "\/placeholder\/"]) }}'.replace('/placeholder/', credentialId);
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('input[name="_token"]')?.value;
                    form.appendChild(csrf);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'PUT';
                    form.appendChild(method);

                    const nameInput = document.createElement('input');
                    nameInput.type = 'hidden';
                    nameInput.name = 'name';
                    nameInput.value = newName;
                    form.appendChild(nameInput);

                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
</x-admin-layouts.admin>
