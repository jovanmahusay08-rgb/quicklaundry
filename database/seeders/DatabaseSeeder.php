<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Customer;
use App\Models\LaundryService;
use App\Models\LoyaltyPoint;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds fresh demo accounts (the original SQL dump's bcrypt password hashes
     * cannot be reversed, so this creates new accounts with known passwords
     * for local testing).
     */
    public function run(): void
    {
        $admin = Admin::create([
            'first_name' => 'Admin',
            'last_name' => 'QuickWash',
            'email' => 'admin@quickwash.com',
            'password' => Hash::make('password123'),
            'phone' => '+63912345678',
            'is_active' => true,
        ]);

        Staff::create([
            'first_name' => 'Jovan',
            'last_name' => 'Mahusay',
            'email' => 'staff@quickwash.com',
            'password' => Hash::make('password123'),
            'phone' => '+639123456780',
            'role' => 'Processor',
            'barangay' => 'Poblacion',
            'is_active' => true,
            'hire_date' => '2024-01-15',
        ]);

        Staff::create([
            'first_name' => 'John',
            'last_name' => 'Illustrisimo',
            'email' => 'driver@quickwash.com',
            'password' => Hash::make('password123'),
            'phone' => '09128916711',
            'role' => 'Driver',
            'barangay' => 'Poblacion',
            'is_active' => true,
            'hire_date' => '2026-05-24',
        ]);

        $customer = Customer::create([
            'first_name' => 'Jovan',
            'last_name' => 'Mahusay',
            'email' => 'customer@quickwash.com',
            'password' => Hash::make('password123'),
            'phone' => '09124568754',
            'address' => '345 Talisay',
            'barangay' => 'Talisay',
            'is_active' => true,
        ]);

        LoyaltyPoint::create(['customer_id' => $customer->id]);

        LaundryService::insert([
            [
                'service_name' => 'Comforter Wash',
                'description' => 'Fresh and deeply cleaned service for comforters, blankets and bulky bedding items.',
                'base_price' => 200.00,
                'price_per_kilo' => 6.00,
                'estimated_days' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Wash and Dry',
                'description' => 'Wash and dry service.',
                'base_price' => 200.00,
                'price_per_kilo' => 30.00,
                'estimated_days' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_name' => 'Regular Wash',
                'description' => 'Fresh, clean, and neatly folded wear with care you can trust.',
                'base_price' => 200.00,
                'price_per_kilo' => 7.00,
                'estimated_days' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Announcement::create([
            'title' => 'Welcome to QuickWash Express',
            'content' => 'Thanks for joining! Book your first pickup and track it in real time from your dashboard.',
            'visible_to' => 'All',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
    }
}
