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
        Schema::create('discount_rule_product_category', function (Blueprint $table) {
            $table->foreignUuid('discount_rule_id')->constrained('discount_rules');
            $table->foreignUuid('product_category_id')->constrained('product_categories');
            if (env('APP_ENV') !== 'testing') {
                $table->unique(['discount_rule_id', 'product_category_id'], 'discount_rule_product_category');
            } else {
                $table->unique(['discount_rule_id', 'product_category_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rule_product_category');
    }
};
