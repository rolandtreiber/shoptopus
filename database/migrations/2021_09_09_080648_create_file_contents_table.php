<?php

use App\Enums\FileType;
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

        Schema::create('file_contents', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('url');
            $table->string('file_name');
            $table->uuidMorphs('fileable');
            $table->string('original_file_name')->nullable();
            $table->string('size', 50)->nullable();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('type')->default(FileType::Image);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_contents');
    }
};
