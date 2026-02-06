<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeEmailRequest;
use App\Mail\ConfirmEmailUsage;
use App\Mail\VerifyNewEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailController extends Controller
{
    /**
     * 显示更改邮箱表单
     */
    public function showChangeForm(): View
    {
        return view('admin.email.change', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * 处理更改邮箱请求
     */
    public function changeEmail(ChangeEmailRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $newEmail = $request->email;

        // 生成令牌并设置待验证邮箱
        $token = $user->setPendingEmail($newEmail);

        // 发送验证邮件到新邮箱
        Mail::to($newEmail)->send(new VerifyNewEmail($user, $token));

        return redirect()
            ->route('admin.email.change')
            ->with('success', '验证邮件已发送至 '.$newEmail.'，请查收并点击链接完成验证。');
    }

    /**
     * 验证新邮箱
     */
    public function verifyNewEmail(Request $request, string $token): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->verifyAndUpdateEmail($token)) {
            return redirect()
                ->route('admin.email.change')
                ->withErrors(['email' => '验证链接无效或已过期，请重新提交更改邮箱请求。']);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', '邮箱地址更新成功！');
    }

    /**
     * 显示邮箱确认页面（180天确认）
     */
    public function showConfirmationForm(): View
    {
        return view('admin.email.confirmation-required', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * 发送确认邮件
     */
    public function sendConfirmationEmail(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 生成确认令牌
        $token = $user->generateEmailConfirmationToken();

        // 发送确认邮件到当前邮箱
        Mail::to($user->email)->send(new ConfirmEmailUsage($user, $token));

        return back()->with('success', '确认邮件已发送至您的邮箱，请查收。');
    }

    /**
     * 确认邮箱正常使用
     */
    public function confirmEmailUsage(Request $request, string $token): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isEmailConfirmationTokenValid($token)) {
            return redirect()
                ->route('admin.email.confirmation')
                ->withErrors(['email' => '确认链接无效或已过期，请重新发送确认邮件。']);
        }

        $user->confirmEmailUsage();

        $intendedUrl = session()->pull('url.intended', route('admin.dashboard'));

        return redirect($intendedUrl)->with('success', '邮箱确认成功，感谢您的配合！');
    }

    /**
     * 重新发送验证邮件
     */
    public function resendVerificationEmail(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (is_null($user->pending_email)) {
            return redirect()
                ->route('admin.email.change')
                ->withErrors(['email' => '没有待验证的邮箱地址，请先提交更改邮箱请求。']);
        }

        // 检查是否已经发送过（60分钟内）
        if ($user->email_confirmation_sent_at !== null && now()->diffInMinutes($user->email_confirmation_sent_at) < 5) {
            return back()->withErrors(['email' => '请稍等几分钟后再试。']);
        }

        // 重新生成令牌并发送
        $token = $user->setPendingEmail($user->pending_email);
        Mail::to($user->pending_email)->send(new VerifyNewEmail($user, $token));

        return back()->with('success', '验证邮件已重新发送，请查收。');
    }

    /**
     * 取消邮箱更改
     */
    public function cancelEmailChange(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->clearPendingEmail();

        return redirect()
            ->route('admin.dashboard')
            ->with('success', '已取消邮箱更改。');
    }
}
