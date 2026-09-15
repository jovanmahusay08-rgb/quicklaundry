<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class Recaptcha
{
    public function validate(Request $request): void
    {
        $data = $request->validate([
            'g-recaptcha-response' => ['required', 'string', 'max:4096'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        if (!config('services.recaptcha.site_key') || !config('services.recaptcha.secret_key')) {
            $this->fail('Login verification is unavailable. Please contact support.');
        }

        try {
            $response = Http::asForm()->connectTimeout(5)->timeout(10)->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $data['g-recaptcha-response'],
                    'remoteip' => $request->ip(),
                ]
            );
        } catch (ConnectionException $exception) {
            $this->fail('Unable to verify reCAPTCHA. Please try again.');
        }

        if (!$response->successful()
            || $response->json('success') !== true
            || strtolower((string) $response->json('hostname')) !== strtolower($request->getHost())) {
            $this->fail('reCAPTCHA verification failed or expired. Please try again.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['g-recaptcha-response' => $message]);
    }
}
