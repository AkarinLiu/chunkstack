<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebauthnCredential;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use lbuchs\WebAuthn\Binary\ByteBuffer;
use lbuchs\WebAuthn\WebAuthn;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function challenge(): View
    {
        $userId = session('2fa:user:id');

        if (! $userId) {
            abort(404);
        }

        $user = User::findOrFail($userId);

        return view('admin.auth.two-factor.challenge', [
            'hasTotp' => $user->hasTotpEnabled(),
            'hasWebauthn' => $user->hasWebauthnCredentials(),
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $userId = session('2fa:user:id');

        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $google2fa = new Google2FA;
        $secret = Crypt::decryptString($user->totp_secret);

        if (! $google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => '验证码无效，请重试']);
        }

        return $this->completeAuthentication($request, $user);
    }

    public function recovery(Request $request): RedirectResponse
    {
        $userId = session('2fa:user:id');

        if (! $userId) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $codes = json_decode($user->totp_recovery_codes, true) ?? [];
        $found = false;
        $remaining = [];

        foreach ($codes as $hashedCode) {
            if (! $found && Hash::check(trim($request->recovery_code), $hashedCode)) {
                $found = true;

                continue;
            }

            $remaining[] = $hashedCode;
        }

        if (! $found) {
            return back()->withErrors(['recovery_code' => '恢复码无效']);
        }

        $user->update([
            'totp_recovery_codes' => json_encode($remaining),
        ]);

        return $this->completeAuthentication($request, $user);
    }

    public function assertionOptions(Request $request): JsonResponse
    {
        $userId = session('2fa:user:id');

        if (! $userId) {
            abort(404);
        }

        $user = User::findOrFail($userId);
        $credentials = $user->webauthnCredentials;

        if ($credentials->isEmpty()) {
            abort(404);
        }

        $webauthn = new WebAuthn(config('app.name'), $request->getHost(), null, true);
        $credentialIds = $credentials->pluck('credential_id')->map(fn ($id) => base64_decode($id))->toArray();
        $getArgs = $webauthn->getGetArgs($credentialIds);

        session(['webauthn:challenge' => $webauthn->getChallenge()->getBinaryString()]);

        return response()->json($getArgs);
    }

    public function verifyWebAuthn(Request $request): JsonResponse
    {
        $userId = session('2fa:user:id');

        if (! $userId) {
            return response()->json(['error' => '未授权'], 401);
        }

        $user = User::findOrFail($userId);

        $challenge = session('webauthn:challenge');

        if (! $challenge) {
            return response()->json(['error' => '挑战已过期，请刷新页面'], 400);
        }

        $clientDataJSON = base64_decode($request->input('response.clientDataJSON'));
        $authenticatorData = base64_decode($request->input('response.authenticatorData'));
        $signature = base64_decode($request->input('response.signature'));
        $credentialId = $request->input('id');
        $credentialIdBase64 = strtr($credentialId, '-_', '+/');
        // 恢复 base64 padding（浏览器返回的 assertion.id 是 base64url 编码，去掉了尾部 =）
        $credentialIdBase64 .= str_repeat('=', (4 - strlen($credentialIdBase64) % 4) % 4);

        $storedCredential = $user->webauthnCredentials()->where('credential_id', $credentialIdBase64)->first();

        if (! $storedCredential) {
            return response()->json(['error' => '未找到凭据'], 404);
        }

        $webauthn = new WebAuthn(config('app.name'), $request->getHost(), null, true);

        try {
            $webauthn->processGet(
                $clientDataJSON,
                $authenticatorData,
                $signature,
                $storedCredential->public_key,
                new ByteBuffer($challenge),
                $storedCredential->counter,
            );

            $newCounter = $webauthn->getSignatureCounter();
            $storedCredential->update(['counter' => $newCounter]);

            session()->forget('webauthn:challenge');

            Auth::login($user);
            session()->forget('2fa:user:id');
            session()->forget('2fa:remember');
            session()->put('2fa:passed', true);

            return response()->json([
                'redirect' => route('admin.dashboard'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => '验证失败: '.$e->getMessage()], 400);
        }
    }

    public function setup(): View
    {
        $user = Auth::user();

        return view('admin.auth.two-factor.setup', [
            'isEnabled' => $user->hasTwoFactorEnabled(),
            'hasTotp' => $user->hasTotpEnabled(),
            'hasWebauthn' => $user->hasWebauthnCredentials(),
            'webauthnCredentials' => $user->webauthnCredentials()->get(),
        ]);
    }

    public function enable(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasTotpEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }

        $google2fa = new Google2FA;
        $secret = $google2fa->generateSecretKey();

        $otpauthUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd,
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($otpauthUrl);

        session(['2fa:pending_secret' => $secret]);

        return view('admin.auth.two-factor.setup', [
            'isEnabled' => false,
            'hasTotp' => false,
            'hasWebauthn' => $user->hasWebauthnCredentials(),
            'webauthnCredentials' => $user->webauthnCredentials()->get(),
            'pendingTotp' => true,
            'secret' => $secret,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    public function confirmEnable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $secret = session('2fa:pending_secret');

        if (! $secret) {
            return redirect()->route('admin.2fa.setup');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $google2fa = new Google2FA;

        if (! $google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => '验证码无效，请重试']);
        }

        // 只在尚未生成恢复码时生成（避免覆盖 WebAuthn 注册时已生成的恢复码）
        $recoveryCodesPlain = [];
        if ($user->totp_recovery_codes === null) {
            $recoveryCodesPlain = $this->generateRecoveryCodes();
        }

        $user->update([
            'two_factor_enabled' => true,
            'totp_secret' => Crypt::encryptString($secret),
        ]);

        session()->forget('2fa:pending_secret');

        return redirect()->route('admin.2fa.setup')
            ->with('success', '双重验证已启用')
            ->with('recovery_codes', $recoveryCodesPlain);
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
        ]);

        $user->update([
            'two_factor_enabled' => false,
            'totp_secret' => null,
            'totp_recovery_codes' => null,
            'two_factor_remember_token' => null,
        ]);

        $user->webauthnCredentials()->delete();

        return redirect()->route('admin.2fa.setup')
            ->with('success', '双重验证已关闭');
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }

        $codesPlain = $this->generateRecoveryCodes();

        return redirect()->route('admin.2fa.setup')
            ->with('success', '恢复码已重新生成')
            ->with('recovery_codes', $codesPlain);
    }

    public function beginRegister(Request $request): JsonResponse
    {
        $user = Auth::user();

        $webauthn = new WebAuthn(config('app.name'), $request->getHost(), null, true);
        $binaryUserId = pack('J', $user->id);
        $existingIds = $user->webauthnCredentials()
            ->pluck('credential_id')
            ->map(fn ($id) => base64_decode($id))
            ->toArray();

        $createArgs = $webauthn->getCreateArgs($binaryUserId, $user->email, $user->name, 60, false, false, null, $existingIds);

        session(['webauthn:registration:challenge' => $webauthn->getChallenge()->getBinaryString()]);

        return response()->json($createArgs);
    }

    public function completeRegister(Request $request): JsonResponse
    {
        $user = Auth::user();

        $challenge = session('webauthn:registration:challenge');

        if (! $challenge) {
            return response()->json(['error' => '挑战已过期，请刷新页面'], 400);
        }

        $clientDataJSON = base64_decode($request->input('response.clientDataJSON'));
        $attestationObject = base64_decode($request->input('response.attestationObject'));

        $webauthn = new WebAuthn(config('app.name'), $request->getHost(), null, true);

        try {
            $data = $webauthn->processCreate($clientDataJSON, $attestationObject, new ByteBuffer($challenge), false, true, false);

            $credential = $user->webauthnCredentials()->create([
                'credential_id' => base64_encode($data->credentialId),
                'public_key' => $data->credentialPublicKey,
                'attestation_type' => $data->attestationFormat,
                'transports' => json_encode($request->input('response.transports', [])),
                'aaguid' => $this->formatAaguid($data->AAGUID),
                'name' => $request->input('name', '安全密钥'),
                'counter' => $data->signatureCounter ?? 0,
            ]);

            session()->forget('webauthn:registration:challenge');

            if (! $user->hasTwoFactorEnabled()) {
                $user->update(['two_factor_enabled' => true]);
            }

            $recoveryCodesPlain = [];

            if ($user->totp_recovery_codes === null) {
                $recoveryCodesPlain = $this->generateRecoveryCodes();
            }

            // 将恢复码 flash 到 session，页面刷新后在网页中显示
            if (! empty($recoveryCodesPlain)) {
                session()->flash('recovery_codes', $recoveryCodesPlain);
                session()->flash('success', '安全密钥注册成功');
            }

            return response()->json([
                'credential' => $credential,
                'reload' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('WebAuthn 注册失败', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return response()->json(['error' => '注册失败 ['.get_class($e).']: '.$e->getMessage()], 400);
        }
    }

    public function removeCredential(Request $request, WebauthnCredential $credential): RedirectResponse
    {
        $user = Auth::user();

        if ($credential->user_id !== $user->id) {
            abort(403);
        }

        $credential->delete();

        if (! $user->hasTotpEnabled() && ! $user->hasWebauthnCredentials()) {
            $user->update([
                'two_factor_enabled' => false,
                'totp_secret' => null,
                'totp_recovery_codes' => null,
            ]);
        }

        return redirect()->route('admin.2fa.setup')
            ->with('success', '安全密钥已删除');
    }

    public function renameCredential(Request $request, WebauthnCredential $credential): RedirectResponse
    {
        $user = Auth::user();

        if ($credential->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $credential->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.2fa.setup')
            ->with('success', '安全密钥已重命名');
    }

    /**
     * 将 16 字节二进制 AAGUID 转为 UUID 格式 (xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx)
     */
    private function formatAaguid(string $binary): string
    {
        if (strlen($binary) < 16) {
            return '00000000-0000-0000-0000-000000000000';
        }

        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($binary, 0, 4)),
            bin2hex(substr($binary, 4, 2)),
            bin2hex(substr($binary, 6, 2)),
            bin2hex(substr($binary, 8, 2)),
            bin2hex(substr($binary, 10, 6)),
        );
    }

    private function generateRecoveryCodes(): array
    {
        $user = Auth::user();

        $hashed = [];
        $plain = [];

        for ($i = 0; $i < 10; $i++) {
            $code = Str::random(10).'-'.Str::random(10).'-'.Str::random(10);
            $hashed[] = Hash::make($code);
            $plain[] = $code;
        }

        $user->update([
            'totp_recovery_codes' => json_encode($hashed),
        ]);

        return $plain;
    }

    private function completeAuthentication(Request $request, User $user): RedirectResponse
    {
        Auth::login($user);

        session()->forget('2fa:user:id');
        session()->forget('2fa:remember');
        session()->put('2fa:passed', true);

        if ($request->boolean('remember_device')) {
            $token = Str::random(60);
            $user->update(['two_factor_remember_token' => hash('sha256', $token)]);

            return redirect()->intended(route('admin.dashboard'))
                ->withCookie(cookie('2fa_remember', $user->id.'|'.$token, 60 * 24 * 30));
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
