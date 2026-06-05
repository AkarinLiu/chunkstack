<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasTwoFactorEnabled() && ! $request->session()->get('2fa:passed')) {
            return redirect()->route('admin.2fa.challenge');
        }

        return $next($request);
    }
}
