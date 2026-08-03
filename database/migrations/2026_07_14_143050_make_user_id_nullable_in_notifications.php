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
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        } else {
            DB::statement('ALTER TABLE notifications MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });
        } else {
            DB::statement('ALTER TABLE notifications MODIFY user_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
