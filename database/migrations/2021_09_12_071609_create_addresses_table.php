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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('address_line_1');
            $table->string('town');
            $table->string('post_code');
            $table->string('slug');
            $table->foreignUuid('user_id')->constrained();
            $table->string('country')->default('UK');
            $table->string('name')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
