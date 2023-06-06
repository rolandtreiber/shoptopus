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

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuidMorphs('payable');
            $table->unsignedDecimal('amount');
            $table->string('slug');
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('payment_source_id')->nullable()->constrained()->nullOnDelete();
            $table->json('proof')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('payment_ref', 150)->nullable();
            $table->string('method_ref', 150)->nullable();
            $table->tinyInteger('type');
            $table->string('description', 250)->nullable();
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
        Schema::dropIfExists('payments');
    }
};
