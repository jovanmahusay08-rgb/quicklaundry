<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('payment_status')->default('Unpaid')->change();
            });
        } else {
            // Use raw SQL to modify the payment_status column from enum to varchar
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status VARCHAR(255) DEFAULT 'Unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('payment_status')->default('Unpaid')->change();
            });
        } else {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('Unpaid', 'Paid', 'Refunded') DEFAULT 'Unpaid'");
        }
    }
};
