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
        // Change payment status from enum to varchar to support flexible status values
        DB::statement("ALTER TABLE payments MODIFY COLUMN status VARCHAR(255) DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending'");
    }
};
