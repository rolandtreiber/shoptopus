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
        Schema::create('payment_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_provider_id')->constrained()->cascadeOnDelete();
            $table->string('setting');
            $table->string('value')->nullable();
            $table->string('test_value')->nullable();
            $table->boolean('public')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_provider_configs');
    }
};
