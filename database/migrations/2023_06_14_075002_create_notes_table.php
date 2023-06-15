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
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('note')->default('');
            $table->string('noteable_type')->nullable();
            $table->uuid('noteable_id')->nullable();
            $table->boolean('public')->default(false);
            $table->index(['noteable_type', 'noteable_id']);
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
