<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (is_null($user->email_changed_at)) {
                $user->update(['email_changed_at' => now()]);
            }

            if ($user->hasTwoFactorEnabled()) {
                if ($this->hasRememberedDevice($request, $user)) {
                    return redirect()->intended(route('admin.dashboard'));
                }

                Auth::logout();
                $request->session()->put('2fa:user:id', $user->id);
                $request->session()->put('2fa:remember', $request->boolean('remember'));

                return redirect()->route('admin.2fa.challenge');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => '邮箱或密码错误',
        ])->onlyInput('email');
    }

    public function showRegistrationForm(): View
    {
        if (User::exists()) {
            abort(404);
        }

        return view('admin.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        if (User::exists()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showChangePasswordForm(): View
    {
        return view('admin.auth.change-password');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', '密码修改成功');
    }

    public function showForgotPasswordForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $user->notify(new ResetPasswordNotification($token));
        }

        return back()->with('success', '如果该邮箱存在于系统中，重置密码链接已发送至您的邮箱');
    }

    public function showResetPasswordForm(Request $request, string $token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => '无效的密码重置链接']);
        }

        if (now()->diffInMinutes($record->created_at) > 5) {
            return back()->withErrors(['email' => '密码重置链接已过期']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('success', '密码重置成功，请使用新密码登录');
    }

    private function hasRememberedDevice(Request $request, User $user): bool
    {
        $cookie = $request->cookie('2fa_remember');

        if (! $cookie || ! $user->two_factor_remember_token) {
            return false;
        }

        $parts = explode('|', $cookie, 2);

        if (count($parts) !== 2 || (int) $parts[0] !== $user->id) {
            return false;
        }

        return hash('sha256', $parts[1]) === $user->two_factor_remember_token;
    }
}
