<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->text('data')->nullable();
            $table->unsignedBigInteger('stock')->default(0);
            $table->string('sku', 50)->unique()->nullable();
            $table->boolean('enabled')->default(true);
            $table->text('description')->nullable();
            $table->decimal('price');
            $table->string('slug');
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
        Schema::dropIfExists('product_variants');
    }
}
