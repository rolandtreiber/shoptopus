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
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->json('name');
            $table->json('description');
            $table->decimal('price')->default(0);
            $table->string('slug');
            $table->boolean('enabled')->default(true);
            $table->boolean('enabled_by_default_on_creation')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('delivery_types');
        Schema::enableForeignKeyConstraints();
    }
};
