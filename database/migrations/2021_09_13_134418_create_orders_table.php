<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('delivery_type_id')->nullable()->constrained();
            $table->foreignUuid('voucher_code_id')->nullable()->constrained();
            $table->foreignUuid('address_id')->constrained();
            $table->unsignedDecimal('original_price')->default(0);
            $table->unsignedDecimal('subtotal')->default(0);
            $table->unsignedDecimal('total_price')->default(0);
            $table->unsignedDecimal('total_discount')->default(0);
            $table->unsignedDecimal('delivery_cost')->default(0);
            $table->integer('status')->default(OrderStatus::Paid);
            $table->string('slug');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
