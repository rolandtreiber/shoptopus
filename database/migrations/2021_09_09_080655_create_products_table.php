<?php

use App\Enums\ProductStatuses;
use App\Facades\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('name');
            $table->text('short_description');
            $table->longtext('description');
            $table->decimal('price');
            $table->tinyInteger('status')->default(ProductStatuses::Provisional);
            $table->unsignedBigInteger('purchase_count')->default(0);
            $table->unsignedBigInteger('stock')->default(0);
            $table->unsignedBigInteger('backup_stock')->nullable()->default(0);
            $table->string('sku', 50)->unique()->nullable();
            $table->uuid('cover_photo_id')->nullable();
            $table->foreign('cover_photo_id')->references('id')->on('file_contents')->nullOnDelete();
            Module::enabled('ratings') && $table->float('rating')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
