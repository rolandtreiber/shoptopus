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

        Schema::create('discount_rules', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('name');
            $table->string('slug');
            $table->tinyInteger('type');
            $table->unsignedDecimal('amount');
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
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
        Schema::dropIfExists('discount_rules');
    }
};
