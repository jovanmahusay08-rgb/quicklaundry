<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RegistrationSms
{
    public function send(string $phone, string $code): void
    {
        if (!config('services.semaphore.api_key')) {
            $this->fail('SMS verification is unavailable. Please contact support.');
        }

        $payload = [
            'apikey' => config('services.semaphore.api_key'),
            'number' => '63'.substr($phone, 1),
            'message' => 'Your QuickWash registration code is {otp}. Valid for 5 minutes. Do not share this code.',
            'code' => $code,
        ];
        if (config('services.semaphore.sender_name')) {
            $payload['sendername'] = config('services.semaphore.sender_name');
        }

        try {
            $response = Http::asForm()->connectTimeout(5)->timeout(15)
                ->post('https://api.semaphore.co/api/v4/otp', $payload);
        } catch (ConnectionException $exception) {
            $this->fail('Unable to send your code. Please wait a minute and try again.');
        }

        if (!$response->successful() || !$response->json('0.message_id')
            || !in_array(strtolower((string) $response->json('0.status')), ['pending', 'queued', 'sent'], true)) {
            $this->fail('Unable to send your code. Please try again later.');
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['phone' => $message]);
    }
}
