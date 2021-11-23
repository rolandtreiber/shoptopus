<?php

use App\Enums\ProductAttributeTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('name');
            $table->tinyInteger('type')->default(ProductAttributeTypes::Text);
            $table->json('image')->nullable();
            $table->boolean('enabled')->default(true);
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
        Schema::dropIfExists('product_attributes');
    }
}
