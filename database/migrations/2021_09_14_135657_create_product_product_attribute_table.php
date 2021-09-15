<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProductAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_product_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class, 'product_id');
            $table->foreignId('product_attribute_id')->constrained('product_attributes');
            $table->foreignId('product_attribute_option_id')->nullable()->constrained('product_attribute_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_product_attribute');
    }
}
