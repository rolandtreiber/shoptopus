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

        Schema::create('contents', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('contentable_type')->nullable();
            $table->uuid('contentable_id')->nullable();
            $table->foreignId('language_id')->constrained();
            $table->tinyInteger('type');
            $table->text('text');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
