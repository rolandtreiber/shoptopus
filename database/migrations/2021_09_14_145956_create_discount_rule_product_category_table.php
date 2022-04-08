<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRuleProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_rule_product_category', function (Blueprint $table) {
            $table->foreignUuid('discount_rule_id')->constrained('discount_rules');
            $table->foreignUuid('product_category_id')->constrained('product_categories');

            $table->unique(['discount_rule_id', 'product_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_rule_product_category');
    }
}
