<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('phone', 20);
            $table->string('profile_image')->nullable();
            $table->enum('role', ['Driver', 'Processor', 'Quality Check'])->default('Driver');
            $table->text('address')->nullable();
            $table->enum('barangay', ['Balidbid', 'Bantigue', 'Langub', 'Maricaban', 'Okoy', 'Poblacion', 'Pooc', 'Talisay'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('hire_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->dateTime('last_login')->nullable();

            $table->index('email', 'idx_staff_email');
            $table->index('role', 'idx_staff_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
