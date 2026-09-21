<?php

namespace App\Services;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class RegistrationPhoneVerification
{
    public function send(Request $request, string $phone, RegistrationSms $sms): void
    {
        $this->locked($request, function () use ($request, $phone, $sms) {
            $phoneKey = 'registration_sms:'.hash('sha256', $phone);
            $lock = Cache::lock($phoneKey.':lock', 30);
            if (!$lock->get()) {
                $this->fail('phone', 'A code is already being sent. Please wait.');
            }
            try {
                if (RateLimiter::tooManyAttempts($phoneKey.':minute', 1)) {
                    $this->fail('phone', 'Please wait 60 seconds before requesting another code.');
                }
                if (RateLimiter::tooManyAttempts($phoneKey.':hour', 5)) {
                    $this->fail('phone', 'Too many code requests for this number. Please try again in an hour.');
                }
                RateLimiter::hit($phoneKey.':minute', 60);
                RateLimiter::hit($phoneKey.':hour', 3600);
                Cache::forget($this->key($request));
                $code = (string) random_int(100000, 999999);
                $sms->send($phone, $code);
                Cache::put($this->key($request), [
                    'phone' => $phone,
                    'code_hash' => Hash::make($code),
                    'attempts' => 0,
                    'verified' => false,
                    'expires_at' => now()->addMinutes(5)->timestamp,
                ], now()->addMinutes(5));
            } finally {
                $lock->release();
            }
        });
    }

    public function verify(Request $request, string $phone, string $code): void
    {
        $this->locked($request, function () use ($request, $phone, $code) {
            $record = Cache::get($this->key($request));
            if (!$record || $record['phone'] !== $phone || $record['expires_at'] <= now()->timestamp) {
                $this->fail('code', 'The code is invalid or expired. Request a new code.');
            }
            if ($record['verified']) {
                $this->fail('code', 'This code has already been used. You can now create your account.');
            }
            if (!Hash::check($code, $record['code_hash'])) {
                $record['attempts']++;
                if ($record['attempts'] >= 5) {
                    Cache::forget($this->key($request));
                } else {
                    Cache::put($this->key($request), $record, max(1, $record['expires_at'] - now()->timestamp));
                }
                $this->fail('code', 'The code is invalid or expired. After 5 attempts, request a new code.');
            }
            $record['verified'] = true;
            unset($record['code_hash']);
            Cache::put($this->key($request), $record, max(1, $record['expires_at'] - now()->timestamp));
        });
    }

    public function register(Request $request, string $phone, Closure $createAccount): mixed
    {
        return $this->locked($request, function () use ($request, $phone, $createAccount) {
            $record = Cache::get($this->key($request));
            if (!$record || !$record['verified'] || $record['phone'] !== $phone || $record['expires_at'] <= now()->timestamp) {
                $this->fail('phone', 'Please verify this phone number with an SMS code before creating your account.');
            }
            $account = $createAccount();
            Cache::forget($this->key($request));
            $request->session()->forget('registration_phone_id');

            return $account;
        });
    }

    private function key(Request $request): string
    {
        if (!$request->session()->has('registration_phone_id')) {
            $request->session()->put('registration_phone_id', (string) \Illuminate\Support\Str::uuid());
        }

        return 'registration_phone:'.hash('sha256', $request->session()->get('registration_phone_id'));
    }

    private function locked(Request $request, Closure $callback): mixed
    {
        $lock = Cache::lock($this->key($request).':lock', 30);
        if (!$lock->get()) {
            $this->fail('phone', 'Another verification request is in progress. Please try again.');
        }
        try {
            return $callback();
        } finally {
            $lock->release();
        }
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
