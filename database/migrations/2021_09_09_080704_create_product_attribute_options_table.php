<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('product_attribute_options', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('product_attribute_id')->nullable()->constrained();
            $table->json('image')->nullable();
            $table->text('name');
            $table->string('common_value')->nullable();
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
        Schema::dropIfExists('product_attribute_options');
    }
}
