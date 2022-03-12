<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_product', function (Blueprint $table) {
            $table->foreignUuid('cart_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('quantity')->default(1);

            $table->unique(['cart_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_product');
    }
}
