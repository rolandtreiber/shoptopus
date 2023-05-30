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
    public function up(): void
    {
        Schema::create('transaction_stripe', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained();
            $table->string('payment_id');
            $table->string('object');
            $table->unsignedBigInteger('amount');
            $table->datetime('canceled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->string('capture_method');
            $table->string('confirmation_method');
            $table->datetime('created');
            $table->string('currency');
            $table->string('description')->nullable();
            $table->string('last_payment_error')->nullable();
            $table->boolean('livemode');
            $table->string('next_action')->nullable();
            $table->string('next_source_action')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_method_types');
            $table->string('receipt_email')->nullable();
            $table->string('setup_future_usage')->nullable();
            $table->string('shipping')->nullable();
            $table->string('source')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_stripe');
    }
};
