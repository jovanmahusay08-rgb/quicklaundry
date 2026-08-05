<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $customer = Auth::guard('customer')->user();
            try {
                $customer->forceFill(['last_login' => now()])->save();
            } catch (\Throwable $exception) {
                report($exception);
            }

            try {
                ActivityLog::record('Customer', $customer->id, 'Customer Login', null, null, 'Successful login');
            } catch (\Throwable $exception) {
                report($exception);
            }

            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        return view('auth.customer-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', Rule::unique('customers', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'digits:11'],
            'address' => ['required', 'string'],
            'barangay' => ['required', Rule::in([
                'Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay',
            ])],
        ]);

        $customer = Customer::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'barangay' => $data['barangay'],
        ]);

        LoyaltyPoint::create(['customer_id' => $customer->id]);

        ActivityLog::record('Customer', $customer->id, 'Customer Registration', null, null, 'New customer registered');

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            ActivityLog::record('Customer', $customer->id, 'Logout', null, null, 'User logged out');
        }

        Auth::guard('customer')->logout();
        if (!Auth::guard('admin')->check() && !Auth::guard('staff')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('customer.login');
    }
}
