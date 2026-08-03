<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('delivery_municipality')->nullable()->after('delivery_address');
            $table->string('delivery_purok')->nullable()->after('delivery_barangay');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['delivery_municipality', 'delivery_purok']);
        });
    }
};
