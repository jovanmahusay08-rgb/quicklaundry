<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->text('delivery_location');
            $table->enum('delivery_barangay', ['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay']);
            $table->foreignId('assigned_driver_id')->nullable()->constrained('staff');
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Failed'])->default('Scheduled');
            $table->string('proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_schedule');
    }
};
