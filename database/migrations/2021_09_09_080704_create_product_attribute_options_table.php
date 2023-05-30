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

        Schema::create('product_attribute_options', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('name');
            $table->string('slug');
            $table->string('value');
            $table->foreignUuid('product_attribute_id')->nullable()->constrained();
            $table->json('image')->nullable();
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
        Schema::dropIfExists('product_attribute_options');
    }
};
