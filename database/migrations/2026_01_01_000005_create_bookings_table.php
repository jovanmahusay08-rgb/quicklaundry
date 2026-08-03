<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('booking_reference', 50)->unique();
            $table->foreignId('service_id')->constrained('laundry_services');
            $table->decimal('quantity_kg', 10, 2)->nullable();
            $table->string('service_type', 50)->nullable();
            $table->date('pickup_date')->nullable();
            $table->time('pickup_time')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->decimal('service_price', 10, 2);
            $table->boolean('is_rush_service')->default(false);
            $table->decimal('rush_fee', 10, 2)->default(0);
            $table->enum('priority_level', ['Normal', 'Rush'])->default('Normal');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', [
                'Pending', 'Pickup Scheduled', 'Picked Up', 'Washing', 'Drying',
                'Folding', 'Ready for Delivery', 'Out for Delivery', 'Completed', 'Cancelled',
            ])->default('Pending');
            $table->enum('payment_status', ['Unpaid', 'Paid', 'Refunded'])->default('Unpaid');
            $table->enum('payment_method', ['Cash on Delivery', 'GCash', 'Bank Transfer'])->default('Cash on Delivery');
            $table->text('delivery_address');
            $table->enum('delivery_barangay', ['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay']);
            $table->string('delivery_phone', 20)->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('special_instructions')->nullable();
            $table->dateTime('estimated_completion')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('staff');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('staff');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->dateTime('completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
