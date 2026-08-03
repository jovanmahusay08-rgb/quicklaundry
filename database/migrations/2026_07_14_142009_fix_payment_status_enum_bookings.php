<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Change payment_status from enum to string to support 'Pending' status
            // This allows more flexibility with payment statuses
            $table->string('payment_status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Refunded'])->default('Unpaid')->change();
        });
    }
};
