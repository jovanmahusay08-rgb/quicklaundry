<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\PasswordResetCode;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetCodeController extends Controller
{
    private const PORTAL_MODELS = [
        'admin' => Admin::class,
        'staff' => Staff::class,
        'customer' => Customer::class,
    ];

    public function showRequestForm(string $portal): View
    {
        $this->modelFor($portal);

        return view('auth.password-reset', ['portal' => $portal, 'step' => 'request']);
    }

    public function sendCode(Request $request, string $portal): RedirectResponse
    {
        $model = $this->modelFor($portal);
        $data = $request->validate(['email' => ['required', 'email']]);
        $email = strtolower($data['email']);
        $account = $model::whereRaw('LOWER(email) = ?', [$email])->first();

        PasswordResetCode::where('portal', $portal)->where('email', $email)->delete();

        if ($account) {
            $code = (string) random_int(100000, 999999);
            PasswordResetCode::create([
                'portal' => $portal,
                'email' => $email,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes(10),
            ]);
            Mail::to($account->email)->send(new PasswordResetCodeMail($code, $portal));
        }

        $request->session()->put([
            'password_reset.portal' => $portal,
            'password_reset.email' => $email,
            'password_reset.verified' => false,
        ]);

        return redirect()->route("{$portal}.password.code")
            ->with('status', 'If that email belongs to an account, a six-digit code has been sent.');
    }

    public function showCodeForm(Request $request, string $portal): View|RedirectResponse
    {
        if (!$this->hasResetSession($request, $portal)) {
            return redirect()->route("{$portal}.password.request");
        }

        return view('auth.password-reset', [
            'portal' => $portal,
            'step' => 'code',
            'email' => $request->session()->get('password_reset.email'),
        ]);
    }

    public function verifyCode(Request $request, string $portal): RedirectResponse
    {
        if (!$this->hasResetSession($request, $portal)) {
            return redirect()->route("{$portal}.password.request");
        }

        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $email = $request->session()->get('password_reset.email');
        $record = PasswordResetCode::where('portal', $portal)
            ->where('email', $email)
            ->latest()
            ->first();

        if (!$record || $record->expires_at->isPast() || $record->attempts >= 5 || !Hash::check($data['code'], $record->code_hash)) {
            if ($record) {
                $record->increment('attempts');
            }

            return back()->withErrors(['code' => 'The code is invalid or has expired. Request a new code and try again.']);
        }

        $record->delete();
        $request->session()->put('password_reset.verified', true);

        return redirect()->route("{$portal}.password.reset");
    }

    public function showResetForm(Request $request, string $portal): View|RedirectResponse
    {
        if (!$this->hasVerifiedResetSession($request, $portal)) {
            return redirect()->route("{$portal}.password.request");
        }

        return view('auth.password-reset', ['portal' => $portal, 'step' => 'reset']);
    }

    public function resetPassword(Request $request, string $portal): RedirectResponse
    {
        if (!$this->hasVerifiedResetSession($request, $portal)) {
            return redirect()->route("{$portal}.password.request");
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $model = $this->modelFor($portal);
        $account = $model::whereRaw('LOWER(email) = ?', [
            $request->session()->get('password_reset.email'),
        ])->firstOrFail();
        $account->forceFill(['password' => Hash::make($data['password'])])->save();
        $request->session()->forget('password_reset');

        return redirect()->route("{$portal}.login")
            ->with('status', 'Your password has been reset. You can now log in.');
    }

    private function modelFor(string $portal): string
    {
        abort_unless(isset(self::PORTAL_MODELS[$portal]), 404);

        return self::PORTAL_MODELS[$portal];
    }

    private function hasResetSession(Request $request, string $portal): bool
    {
        return $request->session()->get('password_reset.portal') === $portal
            && is_string($request->session()->get('password_reset.email'));
    }

    private function hasVerifiedResetSession(Request $request, string $portal): bool
    {
        return $this->hasResetSession($request, $portal)
            && $request->session()->get('password_reset.verified') === true;
    }
}
