<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showLoginForm(Request $request, LoginSecurity $security)
    {
        return view('auth.staff-login', [
            'showCaptcha' => $security->requiresCaptcha('staff', $request, old('email')),
        ]);
    }

    public function login(Request $request, LoginSecurity $security)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $security->enforceCaptcha($request, 'staff', $credentials['email']);

        // Check if the staff exists and is active
        $staff = \App\Models\Staff::where('email', $credentials['email'])->first();
        if ($staff && !$staff->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Contact administrator.',
            ])->onlyInput('email');
        }

        if (Auth::guard('staff')->attempt($credentials, $request->boolean('remember'))) {
            $security->clear('staff', $request, $credentials['email']);
            $request->session()->regenerate();

            $staff = Auth::guard('staff')->user();
            $staff->forceFill(['last_login' => now()])->save();

            ActivityLog::record('Staff', $staff->id, 'Staff Login', null, null, 'Successful login');

            return redirect()->intended(route('staff.dashboard'));
        }

        $security->recordFailure('staff', $request, $credentials['email']);

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
