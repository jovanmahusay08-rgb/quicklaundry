<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();
            $admin->forceFill(['last_login' => now()])->save();

            ActivityLog::record('Admin', $admin->id, 'Admin Login', null, null, 'Successful login');

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our admin records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            ActivityLog::record('Admin', $admin->id, 'Logout', null, null, 'User logged out');
        }

        Auth::guard('admin')->logout();
        if (!Auth::guard('staff')->check() && !Auth::guard('customer')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login');
    }
}
