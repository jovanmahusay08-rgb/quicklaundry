<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['Admin', 'Staff', 'Customer']);
            $table->unsignedBigInteger('user_id');
            $table->string('title', 150);
            $table->text('message');
            $table->enum('type', ['Booking', 'Pickup', 'Delivery', 'Payment', 'System', 'Promo'])->default('System');
            $table->foreignId('related_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
