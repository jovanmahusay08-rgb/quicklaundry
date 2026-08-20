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
        $response = $this->post('/customer/register', [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'email' => 'alice@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone' => '09123456789',
            'address' => '123 Main St',
            'barangay' => 'Poblacion',
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertDatabaseHas('customers', ['email' => 'alice@example.com']);
        $this->assertAuthenticated('customer');

        $this->post('/customer/logout');
        $this->assertGuest('customer');

        $loginResponse = $this->post('/customer/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect('/customer/dashboard');
        $this->assertAuthenticatedAs(Customer::where('email', 'alice@example.com')->first(), 'customer');
    }
}
