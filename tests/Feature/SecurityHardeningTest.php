<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_responses_include_browser_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("frame-ancestors 'none'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_authenticated_pages_cannot_be_stored_in_browser_caches(): void
    {
        $customer = $this->customer('cache@example.com');

        $response = $this->actingAs($customer, 'customer')->get(route('customer.dashboard'));
        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }

    public function test_payment_proofs_are_only_available_to_authorized_accounts(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('payment-proofs/private-proof.png', 'private receipt');
        $owner = $this->customer('owner@example.com');
        $otherCustomer = $this->customer('other@example.com');
        $service = LaundryService::create([
            'service_name' => 'Secure Wash', 'base_price' => 100, 'price_per_kilo' => 20,
            'estimated_days' => 2, 'is_active' => true,
        ]);
        $booking = Booking::create([
            'customer_id' => $owner->id, 'booking_reference' => 'BK-SECURITY-1',
            'service_id' => $service->id, 'service_price' => 100, 'subtotal' => 100,
            'total_amount' => 100, 'delivery_address' => 'Main Street',
            'delivery_barangay' => 'Poblacion',
        ]);
        $payment = Payment::create([
            'booking_id' => $booking->id, 'payment_reference' => 'PAY-SECURITY-1',
            'amount' => 100, 'payment_method' => 'GCash', 'status' => 'Pending',
            'proof_image' => 'payment-proofs/private-proof.png',
        ]);

        $this->get(route('payments.proof', $payment))->assertForbidden();
        $this->actingAs($otherCustomer, 'customer')->get(route('payments.proof', $payment))->assertForbidden();
        auth('customer')->logout();
        $response = $this->actingAs($owner, 'customer')->get(route('payments.proof', $payment));
        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }

    public function test_deactivated_privileged_accounts_are_denied_access(): void
    {
        $admin = Admin::create([
            'first_name' => 'Disabled', 'last_name' => 'Admin', 'email' => 'disabled-admin@example.com',
            'password' => 'secure-password', 'is_active' => false,
        ]);
        $customer = $this->customer('disabled-customer@example.com');
        $customer->update(['is_active' => false]);

        $this->post(route('admin.login'), [
            'email' => $admin->email, 'password' => 'secure-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest('admin');

        $this->post(route('customer.login'), [
            'email' => $customer->email, 'password' => 'secure-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest('customer');
    }

    private function customer(string $email): Customer
    {
        return Customer::create([
            'first_name' => 'Secure', 'last_name' => 'Customer', 'email' => $email,
            'password' => 'secure-password', 'phone' => '09123456789',
            'address' => 'Main Street', 'barangay' => 'Poblacion',
        ]);
    }
}
