<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->enum('status', [
                'Pending', 'Pickup Scheduled', 'Picked Up', 'Washing', 'Drying',
                'Folding', 'Ready for Delivery', 'Out for Delivery', 'Completed', 'Cancelled',
            ]);
            $table->timestamp('timestamp')->useCurrent();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_tracking');
    }
};
