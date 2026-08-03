<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('admin*')) {
            return route('admin.login');
        }

        if ($request->is('staff*')) {
            return route('staff.login');
        }

        if (!$request->expectsJson()) {
            return route('customer.login');
        }

        return null;
    }
}
