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

    public function test_android_customer_dashboard_has_a_working_logout_button(): void
    {
        $customer = Customer::create([
            'first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'logout@example.com',
            'password' => Hash::make('secret123'), 'phone' => '09171234567',
            'address' => '123 Sample Street', 'barangay' => 'Poblacion',
        ]);

        $this->actingAs($customer, 'customer')
            ->withHeader('User-Agent', 'QuickWashCustomer/1.0')
            ->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee('aria-label="Log out"', false)
            ->assertSee(route('customer.logout'), false);

        $this->actingAs($customer, 'customer')
            ->post(route('customer.logout'))
            ->assertRedirect(route('customer.login'));

        $this->assertGuest('customer');
    }

    public function test_customer_can_share_location_only_for_their_active_booking(): void
    {
        $customer = Customer::create([
            'first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'location@example.com',
            'password' => Hash::make('secret123'), 'phone' => '09171234567',
            'address' => '123 Sample Street', 'barangay' => 'Poblacion',
        ]);
        $otherCustomer = Customer::create([
            'first_name' => 'Ana', 'last_name' => 'Reyes', 'email' => 'other@example.com',
            'password' => Hash::make('secret123'), 'phone' => '09179876543',
            'address' => '456 Sample Street', 'barangay' => 'Poblacion',
        ]);
        $service = LaundryService::create([
            'service_name' => 'Wash & Fold', 'base_price' => 120, 'price_per_kilo' => 30,
            'estimated_days' => 2, 'is_active' => true,
        ]);
        $bookingData = [
            'booking_reference' => 'BK-LOCATION-1', 'service_id' => $service->id,
            'quantity_kg' => 3, 'service_type' => 'Wash & Fold', 'service_price' => 120,
            'subtotal' => 120, 'discount' => 0, 'total_amount' => 120, 'status' => 'Pending',
            'payment_status' => 'Unpaid', 'delivery_address' => '123 Sample Street',
            'delivery_barangay' => 'Poblacion', 'delivery_phone' => '09171234567',
        ];
        $booking = Booking::create(['customer_id' => $customer->id] + $bookingData);
        $otherBooking = Booking::create(['customer_id' => $otherCustomer->id] + array_merge($bookingData, ['booking_reference' => 'BK-LOCATION-2']));

        $this->actingAs($customer, 'customer');

        $this->postJson("/customer/bookings/{$booking->id}/location", [
            'latitude' => 11.1547001, 'longitude' => 123.8056001, 'accuracy' => 8.5,
        ])->assertOk()->assertJsonPath('accuracy', 8.5);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id, 'live_latitude' => 11.1547001, 'live_longitude' => 123.8056001,
        ]);
        $this->postJson("/customer/bookings/{$otherBooking->id}/location", [
            'latitude' => 11.1547, 'longitude' => 123.8056,
        ])->assertForbidden();
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
