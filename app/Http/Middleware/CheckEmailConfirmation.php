<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmailConfirmation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        // 排除不需要检查的路由
        $excludedRoutes = [
            'admin.email.confirmation',
            'admin.email.confirmation.send',
            'admin.email.confirmation.verify',
            'admin.email.change',
            'admin.email.change.submit',
            'admin.email.verify',
            'admin.logout',
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        // 检查是否需要确认邮箱（180天）
        if ($user->needsEmailConfirmation()) {
            // 保存当前URL以便确认后重定向回来
            session()->put('url.intended', $request->fullUrl());

            return redirect()->route('admin.email.confirmation');
        }

        return $next($request);
    }
}
