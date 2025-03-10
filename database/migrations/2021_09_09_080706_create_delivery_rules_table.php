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
        Schema::create('delivery_rules', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('delivery_type_id')->constrained();
            $table->json('postcodes')->nullable();
            $table->unsignedBigInteger('min_weight')->nullable();
            $table->unsignedBigInteger('max_weight')->nullable();
            $table->unsignedBigInteger('min_distance')->nullable();
            $table->unsignedBigInteger('max_distance')->nullable();
            $table->string('distance_unit')->default('meter');
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->json('countries')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('slug');
            $table->boolean('enabled')->default(true);
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
        Schema::dropIfExists('delivery_rules');
        Schema::enableForeignKeyConstraints();
    }
};
