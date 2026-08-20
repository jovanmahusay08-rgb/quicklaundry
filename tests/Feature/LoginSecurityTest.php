<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Staff;
use App\Services\LoginSecurity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LoginSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'cache.default' => 'array',
            'services.recaptcha.site_key' => 'test-site-key',
            'services.recaptcha.secret_key' => 'test-secret-key',
            'services.recaptcha.hostname' => 'quickwashsystem.com',
        ]);
        Cache::flush();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_every_portal_requires_recaptcha_after_three_failed_attempts(): void
    {
        $accounts = [
            'admin' => Admin::create([
                'first_name' => 'Ada', 'last_name' => 'Admin', 'email' => 'admin-security@example.com',
                'password' => 'correct-password',
            ]),
            'staff' => Staff::create([
                'first_name' => 'Sam', 'last_name' => 'Staff', 'email' => 'staff-security@example.com',
                'password' => 'correct-password', 'phone' => '09123456789', 'is_active' => true,
            ]),
            'customer' => Customer::create([
                'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'customer-security@example.com',
                'password' => 'correct-password', 'phone' => '09123456789', 'address' => 'Main Street',
                'barangay' => 'Poblacion',
            ]),
        ];

        foreach ($accounts as $portal => $account) {
            for ($attempt = 0; $attempt < 3; $attempt++) {
                $this->post(route("{$portal}.login"), [
                    'email' => $account->email,
                    'password' => 'wrong-password',
                ])->assertSessionHasErrors('email');
            }

            $this->get(route("{$portal}.login"))
                ->assertOk()
                ->assertSee('Security verification is required')
                ->assertSee('test-site-key');

            $this->post(route("{$portal}.login"), [
                'email' => $account->email,
                'password' => 'correct-password',
            ])->assertSessionHasErrors('g-recaptcha-response');
        }
    }

    public function test_valid_recaptcha_allows_login_and_clears_failed_attempts(): void
    {
        $security = new class extends LoginSecurity
        {
            public array $verifiedTokens = [];

            protected function verifyToken(string $secret, string $token, ?string $ip): array
            {
                $this->verifiedTokens[] = compact('secret', 'token', 'ip');

                return ['success' => true, 'hostname' => 'quickwashsystem.com'];
            }
        };
        $this->app->instance(LoginSecurity::class, $security);
        $customer = Customer::create([
            'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'secured@example.com',
            'password' => 'correct-password', 'phone' => '09123456789', 'address' => 'Main Street',
            'barangay' => 'Poblacion',
        ]);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('customer.login'), [
                'email' => $customer->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('customer.login'), [
            'email' => $customer->email,
            'password' => 'correct-password',
            'g-recaptcha-response' => 'valid-single-use-token',
        ])->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer, 'customer');

        $this->assertCount(1, $security->verifiedTokens);
        $this->assertSame('test-secret-key', $security->verifiedTokens[0]['secret']);
        $this->assertSame('valid-single-use-token', $security->verifiedTokens[0]['token']);
    }

    public function test_missing_recaptcha_keys_never_lock_users_behind_an_unusable_widget(): void
    {
        config([
            'services.recaptcha.site_key' => null,
            'services.recaptcha.secret_key' => null,
        ]);
        $customer = Customer::create([
            'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'no-captcha@example.com',
            'password' => 'correct-password', 'phone' => '09123456789', 'address' => 'Main Street',
            'barangay' => 'Poblacion',
        ]);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('customer.login'), [
                'email' => $customer->email,
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->get(route('customer.login'))
            ->assertOk()
            ->assertDontSee('reCAPTCHA is not configured');

        $this->post(route('customer.login'), [
            'email' => $customer->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer, 'customer');
    }
}
