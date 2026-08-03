<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return match ($guard) {
                    'admin' => redirect()->route('admin.dashboard'),
                    'staff' => redirect()->route('staff.dashboard'),
                    'customer' => redirect()->route('customer.dashboard'),
                    default => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}
