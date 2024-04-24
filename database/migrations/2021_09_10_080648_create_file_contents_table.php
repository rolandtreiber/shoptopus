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

        // The product_id field is a helper field to make it simpler to get all images for the main product through all its variants.
        // Without a direct link to the product, we'd have to find the parent product through its variants which is a costly database operation.
        // The downside is that in any case where the file is not linked to a product variant, this field is redundant.
        Schema::create('file_contents', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('product_id')->nullable()->constrained()->nullOnDelete();
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
