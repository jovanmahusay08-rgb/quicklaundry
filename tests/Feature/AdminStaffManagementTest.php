<?php

namespace Tests\Feature;

use App\Models\Admin;
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
            'password' => 'temporary-password',
            'password_confirmation' => 'temporary-password',
        ]);

        $response->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success', 'Staff account created successfully.');
        $staff = Staff::where('email', 'stella@example.com')->firstOrFail();
        $this->assertSame('Processor', $staff->role);
        $this->assertTrue($staff->is_active);
        $this->assertTrue(Hash::check('temporary-password', $staff->password));
    }

    public function test_guest_cannot_create_a_staff_account(): void
    {
        $this->post(route('admin.staff.store'), [
            'first_name' => 'Blocked',
            'last_name' => 'User',
            'email' => 'blocked@example.com',
            'phone' => '09123456789',
            'role' => 'Driver',
            'password' => 'temporary-password',
            'password_confirmation' => 'temporary-password',
        ])->assertRedirect(route('admin.login'));

        $this->assertDatabaseMissing('staff', ['email' => 'blocked@example.com']);
    }
}
