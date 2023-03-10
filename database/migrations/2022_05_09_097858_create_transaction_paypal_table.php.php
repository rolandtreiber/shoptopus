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
        Schema::create('transaction_paypal', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained();
            $table->integer('status_code');
            $table->string('transaction_id');
            $table->string('intent');
            $table->string('status');
            $table->string('reference_id');
            $table->decimal('charge_amount', 10, 2);
            $table->string('currency_code');
            $table->string('merchant_id');
            $table->string('merchant_email');
            $table->string('soft_descriptor')->nullable();
            $table->string('payer_firstname');
            $table->string('payer_surname');
            $table->string('payer_email');
            $table->string('payer_id');
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
        Schema::dropIfExists('transaction_paypal');
    }
};
