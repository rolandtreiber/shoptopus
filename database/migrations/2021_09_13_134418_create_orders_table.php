<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('address_id')->constrained();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('delivery_type_id')->nullable()->constrained();
            $table->foreignUuid('voucher_code_id')->nullable()->constrained();
            $table->unsignedDecimal('original_price')->default(0);
            $table->unsignedDecimal('subtotal')->default(0);
            $table->unsignedDecimal('total_price')->default(0);
            $table->unsignedDecimal('total_discount')->default(0);
            $table->unsignedDecimal('delivery_cost')->default(0);
            $table->string('currency_code')->default('GBP');
            $table->integer('status')->default(OrderStatus::AwaitingPayment);
            $table->string('slug');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
