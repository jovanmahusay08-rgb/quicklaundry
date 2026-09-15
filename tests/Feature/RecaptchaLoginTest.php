<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.recaptcha.site_key' => 'test-site', 'services.recaptcha.secret_key' => 'test-secret']);
        Http::preventStrayRequests();
    }

    public function test_every_portal_displays_the_widget_and_allows_google_frames(): void
    {
        foreach (['admin', 'staff', 'customer'] as $portal) {
            $response = $this->get("/$portal/login");
            $response->assertOk()->assertSee('data-sitekey="test-site"', false)
                ->assertSee('https://www.google.com/recaptcha/api.js', false);
            $this->assertStringContainsString('frame-src https://www.google.com/recaptcha/', $response->headers->get('Content-Security-Policy'));
        }
    }

    public function test_every_portal_rejects_missing_tokens_without_contacting_google(): void
    {
        foreach (['admin', 'staff', 'customer'] as $portal) {
            $this->post("/$portal/login", ['email' => 'user@example.com', 'password' => 'password'])
                ->assertSessionHasErrors('g-recaptcha-response');
            $this->assertGuest($portal);
        }
        Http::assertNothingSent();
    }

    public function test_every_portal_rejects_invalid_expired_or_wrong_host_tokens(): void
    {
        foreach ([['success' => false, 'error-codes' => ['timeout-or-duplicate']], ['success' => true, 'hostname' => 'other.example']] as $result) {
            Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::response($result)]);
            foreach (['admin', 'staff', 'customer'] as $portal) {
                $this->post("/$portal/login", ['email' => 'user@example.com', 'password' => 'password', 'g-recaptcha-response' => 'invalid-token'])
                    ->assertSessionHasErrors('g-recaptcha-response');
                $this->assertGuest($portal);
            }
        }
    }

    public function test_google_connection_failure_blocks_login(): void
    {
        Http::fake(['www.google.com/recaptcha/api/siteverify' => Http::failedConnection()]);
        $this->post('/customer/login', ['g-recaptcha-response' => 'token'])
            ->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest('customer');
    }

    public function test_missing_configuration_blocks_login(): void
    {
        config(['services.recaptcha.secret_key' => null]);
        $this->post('/customer/login', ['g-recaptcha-response' => 'token'])
            ->assertSessionHasErrors('g-recaptcha-response');
        Http::assertNothingSent();
    }
}
