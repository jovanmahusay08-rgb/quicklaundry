<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('live_latitude', 10, 7)->nullable()->after('location_longitude');
            $table->decimal('live_longitude', 10, 7)->nullable()->after('live_latitude');
            $table->decimal('location_accuracy', 10, 2)->nullable()->after('live_longitude');
            $table->timestamp('location_updated_at')->nullable()->after('location_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['live_latitude', 'live_longitude', 'location_accuracy', 'location_updated_at']);
        });
    }
};
