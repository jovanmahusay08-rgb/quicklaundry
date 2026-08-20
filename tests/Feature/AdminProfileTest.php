<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_their_own_details_with_current_password(): void
    {
        $admin = Admin::create([
            'first_name' => 'Main', 'last_name' => 'Admin', 'email' => 'admin@example.com',
            'phone' => '09123456789', 'password' => 'secure-password',
        ]);

        $this->actingAs($admin, 'admin')->put(route('admin.settings.profile'), [
            'first_name' => 'Jovan', 'last_name' => 'Mahusay', 'email' => 'jovan@example.com',
            'phone' => '09987654321', 'current_password' => 'secure-password',
        ])->assertRedirect()->assertSessionHas('success', 'Admin details updated successfully.');

        $admin->refresh();
        $this->assertSame('Jovan', $admin->first_name);
        $this->assertSame('Mahusay', $admin->last_name);
        $this->assertSame('jovan@example.com', $admin->email);
        $this->assertSame('09987654321', $admin->phone);
    }

    public function test_admin_details_cannot_be_updated_with_wrong_password(): void
    {
        $admin = Admin::create([
            'first_name' => 'Main', 'last_name' => 'Admin', 'email' => 'admin@example.com',
            'password' => 'secure-password',
        ]);

        $this->actingAs($admin, 'admin')->put(route('admin.settings.profile'), [
            'first_name' => 'Changed', 'last_name' => 'Admin', 'email' => 'changed@example.com',
            'phone' => '', 'current_password' => 'wrong-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame('Main', $admin->fresh()->first_name);
        $this->assertSame('admin@example.com', $admin->fresh()->email);
    }

    public function test_guest_cannot_update_admin_details(): void
    {
        $this->put(route('admin.settings.profile'), [])->assertRedirect(route('admin.login'));
    }
}
