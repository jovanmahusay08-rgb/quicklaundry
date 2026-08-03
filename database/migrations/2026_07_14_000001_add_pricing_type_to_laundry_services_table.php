<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laundry_services', function (Blueprint $table) {
            $table->string('pricing_type')->default('variable')->after('price_per_kilo');
        });
    }

    public function down()
    {
        Schema::table('laundry_services', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });
    }
};
