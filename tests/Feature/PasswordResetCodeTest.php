<?php

namespace Tests\Feature;

use App\Mail\PasswordResetCodeMail;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_each_portal_can_reset_a_password_with_an_emailed_code(): void
    {
        Mail::fake();

        $accounts = [
            'admin' => Admin::create([
                'first_name' => 'Ada', 'last_name' => 'Admin', 'email' => 'admin-reset@example.com',
                'password' => 'old-password',
            ]),
            'staff' => Staff::create([
                'first_name' => 'Sam', 'last_name' => 'Staff', 'email' => 'staff-reset@example.com',
                'password' => 'old-password', 'phone' => '09123456789',
            ]),
            'customer' => Customer::create([
                'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'customer-reset@example.com',
                'password' => 'old-password', 'phone' => '09123456789', 'address' => 'Main Street',
                'barangay' => 'Poblacion',
            ]),
        ];

        foreach ($accounts as $portal => $account) {
            $this->post(route("{$portal}.password.email"), ['email' => $account->email])
                ->assertRedirect(route("{$portal}.password.code"));

            $code = null;
            Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use ($portal, $account, &$code) {
                if ($mail->portal !== $portal || !$mail->hasTo($account->email)) {
                    return false;
                }

                $code = $mail->code;

                return true;
            });

            $this->post(route("{$portal}.password.verify"), ['code' => $code])
                ->assertRedirect(route("{$portal}.password.reset"));

            $this->post(route("{$portal}.password.update"), [
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])->assertRedirect(route("{$portal}.login"));

            $this->assertTrue(Hash::check('new-secure-password', $account->fresh()->password));
        }
    }

    public function test_an_invalid_code_does_not_allow_a_password_reset(): void
    {
        Mail::fake();
        $customer = Customer::create([
            'first_name' => 'Casey', 'last_name' => 'Customer', 'email' => 'invalid-code@example.com',
            'password' => 'old-password', 'phone' => '09123456789', 'address' => 'Main Street',
            'barangay' => 'Poblacion',
        ]);

        $this->post(route('customer.password.email'), ['email' => $customer->email])
            ->assertRedirect(route('customer.password.code'));
        $this->post(route('customer.password.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');
        $this->get(route('customer.password.reset'))
            ->assertRedirect(route('customer.password.request'));
    }
}
