<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationPhoneTest extends TestCase
{
    use RefreshDatabase;

    private bool $smsFails = false;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.recaptcha.site_key' => 'test-site',
            'services.recaptcha.secret_key' => 'test-secret',
            'services.semaphore.api_key' => 'test-sms-key',
        ]);
        Http::preventStrayRequests();
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'hostname' => 'localhost']),
            'api.semaphore.co/api/v4/otp' => fn () => $this->smsFails
                ? Http::response(['error' => 'No credits'], 400)
                : Http::response([['message_id' => 123, 'status' => 'Pending']]),
        ]);
    }

    private function sendCode(string $phone = '09123456789'): string
    {
        $this->postJson('/customer/register/send-phone-code', ['phone' => $phone, 'g-recaptcha-response' => 'valid-token'])->assertOk();
        return Http::recorded(fn ($request) => str_contains($request->url(), 'semaphore.co'))->last()[0]['code'];
    }

    private function verifyCode(string $code, string $phone = '09123456789')
    {
        return $this->postJson('/customer/register/verify-phone-code', ['phone' => $phone, 'code' => $code]);
    }

    private function registration(string $phone = '09123456789'): array
    {
        return [
            'first_name' => 'Alice', 'last_name' => 'Smith', 'email' => 'alice@example.com',
            'password' => 'Secret!12345', 'password_confirmation' => 'Secret!12345',
            'phone' => $phone, 'address' => '123 Main St', 'barangay' => 'Poblacion',
        ];
    }

    public function test_registration_requires_verified_phone_even_if_client_claims_verification(): void
    {
        $this->post('/customer/register', $this->registration() + ['phone_verified' => true])
            ->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('customers', 0);
        $this->assertGuest('customer');
    }

    public function test_sms_uses_international_number_and_registration_requires_correct_code(): void
    {
        $code = $this->sendCode();
        Http::assertSent(fn ($request) => str_contains($request->url(), 'semaphore.co')
            && $request['number'] === '639123456789' && $request['apikey'] === 'test-sms-key');
        $this->post('/customer/register', $this->registration())->assertSessionHasErrors('phone');
        $this->verifyCode('000000')->assertUnprocessable()->assertJsonValidationErrors('code');
        $this->verifyCode($code)->assertOk();
        $this->verifyCode($code)->assertUnprocessable()->assertJsonValidationErrors('code');
        $this->post('/customer/register', $this->registration())->assertRedirect('/customer/dashboard');
        $this->assertDatabaseCount('customers', 1);
        $this->assertAuthenticated('customer');
    }

    public function test_phone_change_cannot_use_another_numbers_verification(): void
    {
        $code = $this->sendCode();
        $this->verifyCode($code, '09987654321')->assertUnprocessable();
        $this->verifyCode($code)->assertOk();
        $this->post('/customer/register', $this->registration('09987654321'))->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_expired_code_and_verified_proof_cannot_create_accounts(): void
    {
        $code = $this->sendCode();
        $this->travel(5)->minutes();
        $this->verifyCode($code)->assertUnprocessable();
        $code = $this->sendCode();
        $this->verifyCode($code)->assertOk();
        $this->travel(5)->minutes();
        $this->post('/customer/register', $this->registration())->assertSessionHasErrors('phone');
    }

    public function test_five_wrong_attempts_invalidate_the_code(): void
    {
        $code = $this->sendCode();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->verifyCode('000000')->assertUnprocessable();
        }
        $this->verifyCode($code)->assertUnprocessable();
    }

    public function test_resends_have_cooldown_and_invalidate_previous_verification(): void
    {
        $code = $this->sendCode();
        $this->verifyCode($code)->assertOk();
        $this->postJson('/customer/register/send-phone-code', ['phone' => '09123456789', 'g-recaptcha-response' => 'valid-token'])
            ->assertUnprocessable()->assertJsonValidationErrors('phone');
        $this->travel(61)->seconds();
        $this->sendCode();
        $this->post('/customer/register', $this->registration())->assertSessionHasErrors('phone');
        Http::assertSentCount(5);
    }

    public function test_sms_provider_failure_does_not_allow_verification(): void
    {
        $this->smsFails = true;
        $this->postJson('/customer/register/send-phone-code', ['phone' => '09123456789', 'g-recaptcha-response' => 'valid-token'])
            ->assertUnprocessable()->assertJsonValidationErrors('phone');
        $this->verifyCode('123456')->assertUnprocessable();
    }

    public function test_missing_sms_credentials_fail_without_sending_sms(): void
    {
        config(['services.semaphore.api_key' => null]);
        $this->postJson('/customer/register/send-phone-code', ['phone' => '09123456789', 'g-recaptcha-response' => 'valid-token'])
            ->assertUnprocessable()->assertJsonValidationErrors('phone');
        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'semaphore.co'));
    }

    public function test_invalid_phone_and_missing_captcha_cannot_send_sms(): void
    {
        $this->postJson('/customer/register/send-phone-code', ['phone' => '08123456789', 'g-recaptcha-response' => 'valid-token'])
            ->assertUnprocessable()->assertJsonValidationErrors('phone');
        $this->postJson('/customer/register/send-phone-code', ['phone' => '09123456789'])
            ->assertUnprocessable()->assertJsonValidationErrors('g-recaptcha-response');
        Http::assertNothingSent();
    }
}
