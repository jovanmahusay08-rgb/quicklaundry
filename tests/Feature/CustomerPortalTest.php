<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_access_dashboard_and_portal_pages(): void
    {
        $customer = Customer::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@example.com',
            'password' => Hash::make('secret123'),
            'phone' => '09171234567',
            'address' => '123 Sample Street',
            'barangay' => 'Poblacion',
        ]);

        $service = LaundryService::create([
            'service_name' => 'Wash & Fold',
            'description' => 'Fast service',
            'base_price' => 120.00,
            'price_per_kilo' => 30.00,
            'estimated_days' => 2,
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'booking_reference' => 'BK-1001',
            'service_id' => $service->id,
            'quantity_kg' => 3.5,
            'service_type' => 'Wash & Fold',
            'service_price' => 120.00,
            'subtotal' => 120.00,
            'discount' => 0,
            'total_amount' => 120.00,
            'status' => 'Pending',
            'payment_status' => 'Unpaid',
            'delivery_address' => '123 Sample Street',
            'delivery_barangay' => 'Poblacion',
            'delivery_phone' => '09171234567',
        ]);

        $this->actingAs($customer, 'customer');

        $this->get('/customer/dashboard')->assertOk()->assertSee('Dashboard');
        $this->get('/customer/bookings')->assertOk()->assertSee('My Bookings');
        $this->get('/customer/bookings/create')->assertOk()->assertSee('Create Booking');
        $this->get('/customer/bookings/' . $booking->id)->assertOk()->assertSee('Booking Details');
        $this->get('/customer/tracking')->assertOk()->assertSee('Order Tracking');
        $this->get('/customer/loyalty')->assertOk()->assertSee('Loyalty Rewards');
        $this->get('/customer/notifications')->assertOk()->assertSee('Notifications');
        $this->get('/customer/profile')->assertOk()->assertSee('My Profile');
    }

    public function test_android_app_download_is_available_from_landing_page(): void
    {
        $this->get('/')->assertOk()->assertSee('Download App');
        $this->get('/app/download')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.android.package-archive')
            ->assertDownload('QuickWash-Customer.apk');
    }

    public function test_customer_can_resume_an_interrupted_gcash_payment(): void
    {
        $customer = Customer::create([
            'first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'resume@example.com',
            'password' => Hash::make('secret123'), 'phone' => '09171234567',
            'address' => '123 Sample Street', 'barangay' => 'Poblacion',
        ]);
        $service = LaundryService::create([
            'service_name' => 'Wash & Fold', 'base_price' => 120, 'price_per_kilo' => 30,
            'estimated_days' => 2, 'is_active' => true,
        ]);
        $booking = Booking::create([
            'customer_id' => $customer->id, 'booking_reference' => 'BK-RESUME',
            'service_id' => $service->id, 'quantity_kg' => 3, 'service_type' => 'Wash & Fold',
            'service_price' => 120, 'subtotal' => 120, 'discount' => 0, 'total_amount' => 120,
            'status' => 'Pending', 'payment_status' => 'Pending',
            'delivery_address' => '123 Sample Street', 'delivery_barangay' => 'Poblacion',
            'delivery_phone' => '09171234567',
        ]);
        $payment = Payment::create([
            'booking_id' => $booking->id, 'payment_reference' => 'PAY-RESUME',
            'amount' => 120, 'payment_method' => 'GCash', 'status' => 'Pending',
        ]);

        $this->actingAs($customer, 'customer')
            ->get(route('customer.bookings.payment.resume', $booking))
            ->assertRedirect(route('customer.payments.gcash', $payment));

        $this->get(route('customer.bookings'))
            ->assertOk()
            ->assertSee('Complete payment');
    }
}
