<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService
{
    protected ?string $apiToken;
    protected string $senderId;
    protected string $apiUrl;

    public function __construct()
    {
        $this->apiToken = trim((string) config('services.philsms.api_token')) ?: null;
        $this->senderId = config('services.philsms.sender_id', 'PhilSMS') ?: 'PhilSMS';
        $this->apiUrl = config('services.philsms.api_url', 'https://dashboard.philsms.com/api/v3/sms/send') ?: 'https://dashboard.philsms.com/api/v3/sms/send';
    }

    /**
     * Send an OTP verification code via PhilSMS.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $message = "Your QuickWash Express verification code is: {$code}. Valid for 10 minutes. Do not share this code with anyone.";

        $result = $this->sendSms($phone, $message);

        return $result['success'] ?? false;
    }

    /**
     * Send an SMS message via PhilSMS v3 API.
     */
    public function sendSms(string $phone, string $message): array
    {
        $recipient = $this->formatPhoneNumber($phone);

        // In local/testing environments without a PhilSMS API token configured,
        // simulate delivery by logging the message to Laravel logs.
        if (empty($this->apiToken)) {
            Log::info("PhilSMS [Simulation] SMS to {$recipient}: {$message}");

            return [
                'success' => true,
                'simulated' => true,
                'message' => 'Simulated delivery (No PHILSMS_API_TOKEN set)',
            ];
        }

        try {
            $response = Http::withToken($this->apiToken)
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->apiUrl, [
                    'recipient' => $recipient,
                    'sender_id' => $this->senderId,
                    'type' => 'plain',
                    'message' => $message,
                ]);

            $data = $response->json();
            $isError = !$response->successful()
                || (is_array($data) && isset($data['status']) && strtolower((string) $data['status']) === 'error');

            if (!$isError) {
                Log::info("PhilSMS sent successfully to {$recipient}");

                return [
                    'success' => true,
                    'status' => $response->status(),
                    'data' => $data,
                ];
            }

            Log::error("PhilSMS error response: {$response->status()} - {$response->body()}");

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => is_array($data) ? ($data['message'] ?? $response->body()) : $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error("PhilSMS exception: {$e->getMessage()}");

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format and normalize a Philippine phone number into 639XXXXXXXXX format for PhilSMS.
     */
    public function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Convert 09XXXXXXXXX to 639XXXXXXXXX
        if (str_starts_with($cleaned, '09') && strlen($cleaned) === 11) {
            $cleaned = '63' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '9') && strlen($cleaned) === 10) {
            $cleaned = '63' . $cleaned;
        }

        return $cleaned;
    }
}

