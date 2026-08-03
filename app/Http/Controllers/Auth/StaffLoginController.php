<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.staff-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the staff exists and is active
        $staff = \App\Models\Staff::where('email', $credentials['email'])->first();
        if ($staff && !$staff->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Contact administrator.',
            ])->onlyInput('email');
        }

        if (Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $staff = Auth::guard('staff')->user();
            $staff->forceFill(['last_login' => now()])->save();

            ActivityLog::record('Staff', $staff->id, 'Staff Login', null, null, 'Successful login');

            return redirect()->intended(route('staff.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our staff records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        if ($staff) {
            ActivityLog::record('Staff', $staff->id, 'Logout', null, null, 'User logged out');
        }

        Auth::guard('staff')->logout();
        if (!Auth::guard('admin')->check() && !Auth::guard('customer')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('staff.login');
    }
}
