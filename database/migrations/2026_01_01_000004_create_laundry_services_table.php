<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name', 100);
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_per_kilo', 10, 2)->nullable();
            $table->integer('estimated_days')->default(2);
            $table->boolean('is_active')->default(true);
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_services');
    }
};
