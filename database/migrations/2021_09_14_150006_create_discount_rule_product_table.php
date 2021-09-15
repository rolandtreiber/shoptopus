<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRuleProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_rule_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_rule_id')->constrained('discount_rules');
            $table->foreignIdFor(Product::class, 'product_id');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
           $table->uuid('model_id')->change();
        });
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->uuid('user_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_rule_product');
    }
}
