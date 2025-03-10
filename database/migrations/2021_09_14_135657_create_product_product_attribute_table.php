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
        Schema::create('product_product_attribute', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('product_attribute_id')->constrained('product_attributes');
            $table->foreignUuid('product_attribute_option_id')->nullable()->constrained('product_attribute_options');

            $table->unique(['product_id', 'product_attribute_id', 'product_attribute_option_id'], 'p_a_o_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_attribute');
    }
};
