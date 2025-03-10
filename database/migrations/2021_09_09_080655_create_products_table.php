<?php

use App\Enums\ProductStatus;
use App\Facades\Module;
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

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('name');
            $table->string('slug');
            $table->text('short_description');
            $table->longtext('description');
            $table->unsignedDecimal('price');
            $table->text('headline')->nullable();
            $table->text('subtitle')->nullable();
            $table->tinyInteger('status')->default(ProductStatus::Provisional);
            $table->unsignedBigInteger('purchase_count')->default(0);
            $table->unsignedBigInteger('stock')->default(0);
            $table->unsignedBigInteger('backup_stock')->nullable()->default(0);
            $table->string('sku', 50)->unique()->nullable();
            $table->boolean('virtual')->nullable()->default(false);
            $table->integer('weight')->nullable()->default(0);
            $table->json('cover_photo')->nullable();
            $table->json('available_attribute_options')->nullable();
            Module::enabled('ratings') && $table->float('rating')->nullable();
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
        Schema::dropIfExists('products');
    }
};
