<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_access_dashboard_and_portal_pages(): void
    {
        $staff = Staff::create([
            'first_name' => 'Ana',
            'last_name' => 'Lopez',
            'email' => 'ana@example.com',
            'password' => Hash::make('secret123'),
            'phone' => '09171234567',
            'role' => 'Driver',
            'address' => '123 Sample Street',
            'barangay' => 'Poblacion',
            'is_active' => true,
        ]);

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
            'assigned_staff_id' => $staff->id,
            'assigned_driver_id' => $staff->id,
        ]);

        $this->actingAs($staff, 'staff');

        $this->get('/staff/dashboard')->assertOk()->assertSee('Assigned Processing Orders');
        $this->get('/staff/bookings')->assertOk()->assertSee('Bookings');
        $this->get('/staff/bookings/' . $booking->id)->assertOk()->assertSee('Booking Details');
        $this->get('/staff/bookings/' . $booking->id . '/status')->assertOk()->assertSee('Update Status');
        $this->get('/staff/pickups')->assertOk()->assertSee('Pickups');
        $this->get('/staff/deliveries')->assertOk()->assertSee('Deliveries');
        $this->get('/staff/receipt/' . $booking->id)->assertOk()->assertSee('Receipt');
        $this->get('/staff/profile')->assertOk()->assertSee('My Profile');
    }
}
