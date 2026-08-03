<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('phone', 20);
            $table->string('profile_image')->nullable();
            $table->text('address');
            $table->enum('barangay', ['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay']);
            $table->boolean('is_active')->default(true);
            $table->integer('loyalty_points')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->dateTime('last_login')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
