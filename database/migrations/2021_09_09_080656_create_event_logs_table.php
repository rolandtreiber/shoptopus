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

        Schema::create('event_logs', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('message', 200);
            $table->tinyInteger('type');
            $table->string('eventable_type')->nullable();
            $table->uuid('eventable_id')->nullable();
            $table->text('data');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
