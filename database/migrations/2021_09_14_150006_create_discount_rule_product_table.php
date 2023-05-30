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
        Schema::create('discount_rule_product', function (Blueprint $table) {
            $table->foreignUuid('discount_rule_id')->constrained();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();

            $table->unique(['discount_rule_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rule_product');
    }
};
