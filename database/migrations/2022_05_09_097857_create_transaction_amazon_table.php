<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_amazon', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained();
            $table->string('request_id');
            $table->string('checkout_session_id');
            $table->string('charge_id');
            $table->string('product_type')->nullable();
            $table->string('merchant_reference_id')->nullable();
            $table->string('merchant_store_name')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_email')->nullable();
            $table->string('buyer_id')->nullable();
            $table->string('state');
            $table->string('reason_code')->nullable();
            $table->string('reason_description')->nullable();
            $table->datetime('amazon_last_updated_timestamp');
//            $table->string('payment_intent');
//            $table->decimal('charge_amount', 10,2);
//            $table->string('currency_code');
//            $table->string('environment');
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
        Schema::dropIfExists('transaction_amazon');
    }
};
