<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('staff')->check()) {
            return redirect()->route('staff.login');
        }

        $user = Auth::guard('staff')->user();
        if ($user && !$user->is_active) {
            Auth::guard('staff')->logout();
            if (!Auth::guard('admin')->check() && !Auth::guard('customer')->check()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('staff.login')->withErrors(['email' => 'Your account is deactivated.']);
        }

        return $next($request);
    }
}
