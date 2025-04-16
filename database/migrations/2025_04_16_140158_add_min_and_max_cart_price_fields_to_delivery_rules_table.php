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
        Schema::table('delivery_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('min_cart_price')->nullable();
            $table->unsignedBigInteger('max_cart_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_rules', function (Blueprint $table) {
            $table->dropColumn('min_cart_price');
            $table->dropColumn('max_cart_price');
        });
    }
};
