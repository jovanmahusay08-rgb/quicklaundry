<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginSecurity
{
    private const CAPTCHA_THRESHOLD = 3;
    private const ATTEMPT_TTL_MINUTES = 15;

    public function requiresCaptcha(string $portal, Request $request, ?string $email = null): bool
    {
        $email ??= $request->session()->get("login_security.email.{$portal}");

        return is_string($email)
            && Cache::get($this->attemptKey($portal, $request->ip(), $email), 0) >= self::CAPTCHA_THRESHOLD;
    }

    public function enforceCaptcha(Request $request, string $portal, string $email): void
    {
        if (!$this->requiresCaptcha($portal, $request, $email)) {
            return;
        }

        $token = $request->validate([
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the security verification.',
        ])['g-recaptcha-response'];

        $secret = config('services.recaptcha.secret_key');
        if (!$secret) {
            Log::critical('reCAPTCHA is required but RECAPTCHA_SECRET_KEY is not configured.');
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Security verification is temporarily unavailable. Please contact support.',
            ]);
        }

        try {
            $verification = $this->verifyToken($secret, $token, $request->ip());
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Security verification could not be completed. Please try again.',
            ]);
        }

        $expectedHostname = config('services.recaptcha.hostname');
        $hostnameMatches = !$expectedHostname || ($verification['hostname'] ?? null) === $expectedHostname;
        if (($verification['success'] ?? false) !== true || !$hostnameMatches) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Security verification failed. Please try again.',
            ]);
        }
    }

    public function recordFailure(string $portal, Request $request, string $email): int
    {
        $key = $this->attemptKey($portal, $request->ip(), $email);
        $attempts = (int) Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(self::ATTEMPT_TTL_MINUTES));
        $request->session()->put("login_security.email.{$portal}", strtolower($email));

        return $attempts;
    }

    public function clear(string $portal, Request $request, string $email): void
    {
        Cache::forget($this->attemptKey($portal, $request->ip(), $email));
        $request->session()->forget("login_security.email.{$portal}");
    }

    private function attemptKey(string $portal, ?string $ip, string $email): string
    {
        return 'login_failures:' . hash('sha256', implode('|', [
            $portal,
            $ip ?: 'unknown',
            strtolower(trim($email)),
        ]));
    }

    protected function verifyToken(string $secret, string $token, ?string $ip): array
    {
        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        if ($curl === false) {
            throw new \RuntimeException('Unable to initialize reCAPTCHA verification.');
        }

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ip,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if (!is_string($body) || $status !== 200) {
            throw new \RuntimeException('reCAPTCHA verification request failed: ' . ($error ?: "HTTP {$status}"));
        }

        $result = json_decode($body, true, flags: JSON_THROW_ON_ERROR);

        return is_array($result) ? $result : [];
    }
}
