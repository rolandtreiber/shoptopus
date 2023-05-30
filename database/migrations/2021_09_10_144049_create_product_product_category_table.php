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
        Schema::create('product_product_category', function (Blueprint $table) {
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_category_id')->constrained()->cascadeOnDelete();

            $table->unique(['product_id', 'product_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_category');
    }
};
