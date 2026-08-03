<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash', 'Credit Card', 'Debit Card', 'Mobile Payment'])->after('amount');
            $table->enum('status', ['Pending', 'Paid', 'Failed'])->default('Pending')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('status');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['Cash on Delivery', 'GCash', 'Bank Transfer'])->after('amount');
            $table->enum('status', ['Pending', 'Completed', 'Failed'])->default('Pending')->after('payment_method');
        });
    }
};
