<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Services\PhilSmsService;
use App\Services\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CustomerLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.customer-login');
    }

    public function login(Request $request, Recaptcha $recaptcha)
    {
        $recaptcha->validate($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $customerAccount = Customer::where('email', $credentials['email'])->first();
        if ($customerAccount && !$customerAccount->is_active) {
            return back()->withErrors(['email' => 'Your account is deactivated.'])->onlyInput('email');
        }

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

    public function showRegistrationForm(Request $request)
    {
        $pending = $request->session()->get('customer_registration_data', []);

        return view('auth.customer-register', [
            'pending' => $pending,
        ]);
    }

    public function register(Request $request, PhilSmsService $smsService)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', Rule::unique('customers', 'email')],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->letters()->numbers()->symbols()],
            'phone' => ['required', 'digits:11', 'regex:/^09\d{9}$/', Rule::unique('customers', 'phone')],
            'address' => ['required', 'string'],
            'barangay' => ['required', Rule::in([
                'Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay',
            ])],
        ], [
            'phone.regex' => 'The mobile number must be an 11-digit Philippine number starting with 09.',
            'phone.unique' => 'This mobile number is already registered.',
        ]);

        $phone = $data['phone'];
        $code = (string) random_int(100000, 999999);
        $cacheKey = "customer_registration_otp_{$phone}";

        Cache::put($cacheKey, [
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $request->session()->put('customer_registration_data', [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'barangay' => $data['barangay'],
        ]);

        $smsService->sendOtp($phone, $code);

        return redirect()->route('customer.register.otp')
            ->with('status', 'A 6-digit verification code has been sent to your mobile number.');
    }

    public function showOtpForm(Request $request)
    {
        $registrationData = $request->session()->get('customer_registration_data');
        if (!$registrationData || empty($registrationData['phone'])) {
            return redirect()->route('customer.register')
                ->withErrors(['phone' => 'Registration session expired. Please fill out the form again.']);
        }

        $phone = $registrationData['phone'];
        $maskedPhone = substr($phone, 0, 4) . '***' . substr($phone, -4);

        return view('auth.customer-verify-otp', [
            'phone' => $phone,
            'maskedPhone' => $maskedPhone,
            'email' => $registrationData['email'],
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $registrationData = $request->session()->get('customer_registration_data');
        if (!$registrationData || empty($registrationData['phone'])) {
            return redirect()->route('customer.register')
                ->withErrors(['phone' => 'Registration session expired. Please start over.']);
        }

        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Please enter the 6-digit verification code.',
            'code.digits' => 'The verification code must be exactly 6 digits.',
        ]);

        $phone = $registrationData['phone'];
        $cacheKey = "customer_registration_otp_{$phone}";
        $record = Cache::get($cacheKey);

        if (!$record || $record['expires_at'] <= now()->timestamp || $record['attempts'] >= 5 || !Hash::check($data['code'], $record['code_hash'])) {
            if ($record) {
                $record['attempts']++;
                if ($record['attempts'] >= 5) {
                    Cache::forget($cacheKey);
                } else {
                    Cache::put($cacheKey, $record, max(1, $record['expires_at'] - now()->timestamp));
                }
            }

            return back()->withErrors(['code' => 'The verification code is invalid or has expired. Please try again or request a new code.']);
        }

        Cache::forget($cacheKey);
        $request->session()->forget('customer_registration_data');

        $customer = Customer::create([
            'first_name' => $registrationData['first_name'],
            'last_name' => $registrationData['last_name'],
            'email' => $registrationData['email'],
            'password' => Hash::make($registrationData['password']),
            'phone' => $registrationData['phone'],
            'phone_verified_at' => now(),
            'address' => $registrationData['address'],
            'barangay' => $registrationData['barangay'],
        ]);

        LoyaltyPoint::create(['customer_id' => $customer->id]);

        ActivityLog::record('Customer', $customer->id, 'Customer Registration', null, null, 'New customer registered and verified via SMS OTP');

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard')
            ->with('status', 'Mobile number verified successfully! Welcome to QuickWash Express.');
    }

    public function resendOtp(Request $request, PhilSmsService $smsService)
    {
        $registrationData = $request->session()->get('customer_registration_data');
        if (!$registrationData || empty($registrationData['phone'])) {
            return redirect()->route('customer.register')
                ->withErrors(['phone' => 'Registration session expired. Please fill out the form again.']);
        }

        $phone = $registrationData['phone'];
        $cacheKey = "customer_registration_otp_{$phone}";

        $code = (string) random_int(100000, 999999);
        Cache::put($cacheKey, [
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $smsService->sendOtp($phone, $code);

        return back()->with('status', 'A new verification code has been sent to your mobile number.');
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
