<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Services\PhilSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerRegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_valid_philippine_mobile_number(): void
    {
        $response = $this->post(route('customer.register'), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'phone' => '123456', // Invalid format
            'address' => 'Poblacion',
            'barangay' => 'Poblacion',
        ]);

        $response->assertSessionHasErrors(['phone']);
    }

    public function test_valid_registration_dispatches_otp_and_redirects_to_verification_screen(): void
    {
        $phone = '09171234567';

        $response = $this->post(route('customer.register'), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'phone' => $phone,
            'address' => 'Sitio Mahusay',
            'barangay' => 'Poblacion',
        ]);

        $response->assertRedirect(route('customer.register.otp'));

        // Verify session holds registration data
        $this->assertTrue(session()->has('customer_registration_data'));
        $this->assertEquals('juan@example.com', session('customer_registration_data.email'));

        // Verify OTP is cached
        $cacheKey = "customer_registration_otp_{$phone}";
        $this->assertTrue(Cache::has($cacheKey));
        $cacheRecord = Cache::get($cacheKey);
        $this->assertArrayHasKey('code_hash', $cacheRecord);
        $this->assertArrayHasKey('expires_at', $cacheRecord);
    }

    public function test_philsms_api_is_called_with_correct_payload_when_token_configured(): void
    {
        config([
            'services.philsms.api_token' => 'test-philsms-token',
            'services.philsms.sender_id' => 'QuickWash',
            'services.philsms.api_url' => 'https://dashboard.philsms.com/api/v3/sms/send',
        ]);

        Http::fake([
            'https://dashboard.philsms.com/api/v3/sms/send' => Http::response([
                'status' => 'success',
                'message' => 'Message sent successfully',
            ], 200),
        ]);

        $phone = '09181234567';

        $this->post(route('customer.register'), [
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'email' => 'maria@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'phone' => $phone,
            'address' => 'Balidbid street',
            'barangay' => 'Balidbid',
        ])->assertRedirect(route('customer.register.otp'));

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return $request->url() === 'https://dashboard.philsms.com/api/v3/sms/send'
                && $request->hasHeader('Authorization', 'Bearer test-philsms-token')
                && $request['recipient'] === '639181234567'
                && $request['sender_id'] === 'QuickWash'
                && $request['type'] === 'plain'
                && str_contains($request['message'], 'QuickWash Express verification code');
        });

    }

    public function test_show_otp_form_displays_masked_phone(): void
    {
        $this->withSession([
            'customer_registration_data' => [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'Password123!@#',
                'phone' => '09171234567',
                'address' => 'Poblacion',
                'barangay' => 'Poblacion',
            ],
        ]);

        $response = $this->get(route('customer.register.otp'));
        $response->assertOk();
        $response->assertSee('Verify Mobile Number');
        $response->assertSee('0917***4567');
    }

    public function test_show_otp_form_without_session_redirects_to_registration(): void
    {
        $response = $this->get(route('customer.register.otp'));
        $response->assertRedirect(route('customer.register'));
    }

    public function test_verify_otp_rejects_invalid_code(): void
    {
        $phone = '09171234567';
        $validCode = '123456';
        $cacheKey = "customer_registration_otp_{$phone}";

        Cache::put($cacheKey, [
            'code_hash' => Hash::make($validCode),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $this->withSession([
            'customer_registration_data' => [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'Password123!@#',
                'phone' => $phone,
                'address' => 'Poblacion',
                'barangay' => 'Poblacion',
            ],
        ]);

        $response = $this->post(route('customer.register.otp.verify'), [
            'code' => '999999', // Wrong code
        ]);

        $response->assertSessionHasErrors(['code']);
        $this->assertDatabaseMissing('customers', ['email' => 'juan@example.com']);
        $this->assertGuest('customer');

        // Check attempts incremented
        $this->assertEquals(1, Cache::get($cacheKey)['attempts']);
    }

    public function test_verify_otp_locks_out_after_five_failed_attempts(): void
    {
        $phone = '09171234567';
        $cacheKey = "customer_registration_otp_{$phone}";

        Cache::put($cacheKey, [
            'code_hash' => Hash::make('123456'),
            'attempts' => 4, // 1 remaining attempt
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $this->withSession([
            'customer_registration_data' => [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'Password123!@#',
                'phone' => $phone,
                'address' => 'Poblacion',
                'barangay' => 'Poblacion',
            ],
        ]);

        $this->post(route('customer.register.otp.verify'), ['code' => '000000']);

        // Cached code should be invalidated
        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_verify_otp_creates_account_and_logs_in_user(): void
    {
        $phone = '09171234567';
        $validCode = '654321';
        $cacheKey = "customer_registration_otp_{$phone}";

        Cache::put($cacheKey, [
            'code_hash' => Hash::make($validCode),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $this->withSession([
            'customer_registration_data' => [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'Password123!@#',
                'phone' => $phone,
                'address' => 'Sitio Mahusay',
                'barangay' => 'Poblacion',
            ],
        ]);

        $response = $this->post(route('customer.register.otp.verify'), [
            'code' => $validCode,
        ]);

        $response->assertRedirect(route('customer.dashboard'));

        // Verify customer created and phone verified
        $customer = Customer::where('email', 'juan@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Juan', $customer->first_name);
        $this->assertEquals($phone, $customer->phone);
        $this->assertNotNull($customer->phone_verified_at);
        $this->assertTrue($customer->isPhoneVerified());

        // Verify authenticated
        $this->assertAuthenticatedAs($customer, 'customer');

        // Verify session and cache cleaned
        $this->assertFalse(session()->has('customer_registration_data'));
        $this->assertNull(Cache::get($cacheKey));
    }

    public function test_resend_otp_generates_new_code(): void
    {
        $phone = '09171234567';
        $oldCode = '111111';
        $cacheKey = "customer_registration_otp_{$phone}";

        Cache::put($cacheKey, [
            'code_hash' => Hash::make($oldCode),
            'attempts' => 2,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $this->withSession([
            'customer_registration_data' => [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'Password123!@#',
                'phone' => $phone,
                'address' => 'Poblacion',
                'barangay' => 'Poblacion',
            ],
        ]);

        $response = $this->post(route('customer.register.otp.resend'));
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $record = Cache::get($cacheKey);
        $this->assertNotNull($record);
        $this->assertEquals(0, $record['attempts']);
        // Verify old code no longer matches
        $this->assertFalse(Hash::check($oldCode, $record['code_hash']));
    }
}
