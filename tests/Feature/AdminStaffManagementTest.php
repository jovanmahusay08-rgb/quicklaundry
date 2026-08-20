<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_staff_account(): void
    {
        $admin = Admin::create([
            'first_name' => 'Ada',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'admin-password',
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.staff.store'), [
            'first_name' => 'Stella',
            'last_name' => 'Staff',
            'email' => 'stella@example.com',
            'phone' => '09123456789',
            'role' => 'Processor',
            'address' => '123 Main Street',
            'barangay' => 'Poblacion',
            'salary' => '15000',
            'hire_date' => '2026-08-20',
            'password' => 'Temporary!2026',
            'password_confirmation' => 'Temporary!2026',
        ]);

        $response->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success', 'Staff account created successfully.');
        $staff = Staff::where('email', 'stella@example.com')->firstOrFail();
        $this->assertSame('Processor', $staff->role);
        $this->assertTrue($staff->is_active);
        $this->assertTrue(Hash::check('Temporary!2026', $staff->password));
    }

    public function test_guest_cannot_create_a_staff_account(): void
    {
        $this->post(route('admin.staff.store'), [
            'first_name' => 'Blocked',
            'last_name' => 'User',
            'email' => 'blocked@example.com',
            'phone' => '09123456789',
            'role' => 'Driver',
            'password' => 'Temporary!2026',
            'password_confirmation' => 'Temporary!2026',
        ])->assertRedirect(route('admin.login'));

        $this->assertDatabaseMissing('staff', ['email' => 'blocked@example.com']);
    }

    public function test_admin_can_edit_and_update_a_staff_account(): void
    {
        $admin = Admin::create([
            'first_name' => 'Ada', 'last_name' => 'Admin', 'email' => 'admin@example.com',
            'password' => 'admin-password',
        ]);
        $staff = Staff::create([
            'first_name' => 'Stella', 'last_name' => 'Staff', 'email' => 'stella@example.com',
            'password' => 'old-password', 'phone' => '09123456789', 'role' => 'Driver',
        ]);

        $this->actingAs($admin, 'admin')->get(route('admin.staff.edit', $staff))
            ->assertOk()
            ->assertSee('Edit Staff Account')
            ->assertSee('stella@example.com');

        $this->put(route('admin.staff.update', $staff), [
            'first_name' => 'Stella',
            'last_name' => 'Updated',
            'email' => 'updated@example.com',
            'phone' => '09987654321',
            'role' => 'Quality Check',
            'address' => 'Updated address',
            'barangay' => 'Pooc',
            'salary' => '18000',
            'hire_date' => '2026-08-21',
            'password' => 'Updated!2026',
            'password_confirmation' => 'Updated!2026',
        ])->assertRedirect(route('admin.staff'));

        $staff->refresh();
        $this->assertSame('Updated', $staff->last_name);
        $this->assertSame('Quality Check', $staff->role);
        $this->assertTrue(Hash::check('Updated!2026', $staff->password));
    }

    public function test_admin_can_delete_staff_and_existing_orders_become_unassigned(): void
    {
        $admin = Admin::create([
            'first_name' => 'Ada', 'last_name' => 'Admin', 'email' => 'admin@example.com',
            'password' => 'admin-password',
        ]);
        $staff = Staff::create([
            'first_name' => 'Stella', 'last_name' => 'Staff', 'email' => 'stella@example.com',
            'password' => 'old-password', 'phone' => '09123456789', 'role' => 'Driver',
        ]);
        $customer = Customer::create([
            'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'customer@example.com',
            'password' => 'customer-password', 'phone' => '09123456789', 'address' => 'Main Street',
            'barangay' => 'Poblacion',
        ]);
        $service = LaundryService::create([
            'service_name' => 'Wash', 'base_price' => 100, 'price_per_kilo' => 20,
            'estimated_days' => 2, 'is_active' => true,
        ]);
        $booking = Booking::create([
            'customer_id' => $customer->id, 'booking_reference' => 'BK-CRUD-1',
            'service_id' => $service->id, 'service_price' => 100, 'subtotal' => 100,
            'total_amount' => 100, 'delivery_address' => 'Main Street',
            'delivery_barangay' => 'Poblacion', 'assigned_staff_id' => $staff->id,
            'assigned_driver_id' => $staff->id,
        ]);

        $this->actingAs($admin, 'admin')->delete(route('admin.staff.destroy', $staff))
            ->assertRedirect(route('admin.staff'));

        $this->assertDatabaseMissing('staff', ['id' => $staff->id]);
        $booking->refresh();
        $this->assertNull($booking->assigned_staff_id);
        $this->assertNull($booking->assigned_driver_id);
    }
}
