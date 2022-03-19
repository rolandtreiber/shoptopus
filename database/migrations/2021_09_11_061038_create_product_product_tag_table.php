<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProductTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->foreignUuid('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('product_tag_id')->nullable()->constrained('product_tags')->cascadeOnDelete();

            $table->unique(['product_id', 'product_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_product_tag');
    }
}
