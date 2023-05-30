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
        Schema::create('order_product', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('amount')->default(1);
            $table->text('name');
            $table->unsignedDecimal('original_unit_price')->default(0);
            $table->unsignedDecimal('unit_price')->default(0);
            $table->unsignedDecimal('full_price')->default(0);
            $table->unsignedDecimal('final_price')->default(0);
            $table->unsignedDecimal('unit_discount')->default(0);
            $table->unsignedDecimal('total_discount')->default(0);
            $table->string('slug');
            $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product');
    }
};
