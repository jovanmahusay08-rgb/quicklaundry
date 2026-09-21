<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_customer_login_shows_account_registration(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertDontSee('Create an Account');
        $this->get(route('staff.login'))->assertOk()->assertDontSee('Create an Account');
        $this->get(route('customer.login'))->assertOk()->assertSee('Create an Account');
    }

    public function test_user_can_register_and_login(): void
    {
        config(['services.recaptcha.site_key' => 'test-site', 'services.recaptcha.secret_key' => 'test-secret']);
        \Illuminate\Support\Facades\Http::fake([
            'www.google.com/recaptcha/api/siteverify' => \Illuminate\Support\Facades\Http::response(['success' => true, 'hostname' => 'localhost']),
            'api.semaphore.co/api/v4/otp' => \Illuminate\Support\Facades\Http::response([['message_id' => 123, 'status' => 'Pending']]),
        ]);
        config(['services.semaphore.api_key' => 'test-sms-key']);
        $this->postJson('/customer/register/send-phone-code', ['phone' => '09123456789', 'g-recaptcha-response' => 'valid-token'])->assertOk();
        $smsRequest = \Illuminate\Support\Facades\Http::recorded(fn ($request) => str_contains($request->url(), 'semaphore.co'))->first()[0];
        $this->postJson('/customer/register/verify-phone-code', ['phone' => '09123456789', 'code' => $smsRequest['code']])->assertOk();
        $response = $this->post('/customer/register', [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'password' => 'Secret!12345',
            'password_confirmation' => 'Secret!12345',
            'phone' => '09123456789',
            'address' => '123 Main St',
            'barangay' => 'Poblacion',
        ]);

        $response->assertRedirect(route('customer.register.otp'));

        \Illuminate\Support\Facades\Cache::put('customer_registration_otp_09123456789', [
            'code_hash' => \Illuminate\Support\Facades\Hash::make('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], now()->addMinutes(10));

        $otpResponse = $this->post('/customer/register/verify-otp', [
            'code' => '123456',
        ]);

        $otpResponse->assertRedirect('/customer/dashboard');
        $this->assertDatabaseHas('customers', ['email' => 'alice@example.com']);
        $this->assertAuthenticated('customer');


        $this->post('/customer/logout');
        $this->assertGuest('customer');

        $loginResponse = $this->post('/customer/login', [
            'g-recaptcha-response' => 'valid-token',
            'email' => 'alice@example.com',
            'password' => 'Secret!12345',
        ]);

        $loginResponse->assertRedirect('/customer/dashboard');
        $this->assertAuthenticatedAs(Customer::where('email', 'alice@example.com')->first(), 'customer');
    }
}
