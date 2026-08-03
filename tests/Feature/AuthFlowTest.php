<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
        $this->assertAuthenticated();

        $this->post('/logout');
        $this->assertGuest();

        $loginResponse = $this->post('/login', [
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'alice@example.com')->first());
    }
}
