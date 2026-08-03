<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gcash_sender_number', 20)->nullable()->after('status');
            $table->string('gcash_reference', 100)->nullable()->after('gcash_sender_number');
            $table->string('proof_image')->nullable()->after('gcash_reference');
            $table->timestamp('submitted_at')->nullable()->after('proof_image');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['gcash_sender_number', 'gcash_reference', 'proof_image', 'submitted_at']);
        });
    }
};
