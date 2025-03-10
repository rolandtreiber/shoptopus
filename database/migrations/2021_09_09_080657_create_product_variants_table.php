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
        Schema::disableForeignKeyConstraints();

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('slug');
            $table->unsignedDecimal('price');
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->text('data')->nullable();
            $table->unsignedBigInteger('stock')->default(0);
            $table->string('sku', 50)->unique()->nullable();
            $table->text('description')->nullable();
            $table->json('attribute_options')->nullable();
            $table->boolean('enabled')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
