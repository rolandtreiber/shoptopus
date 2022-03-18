<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('payable_type');
            $table->uuid('payable_id');
            $table->unsignedDecimal('amount');
            $table->string('slug');
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('payment_source_id')->nullable()->constrained()->nullOnDelete();
            $table->json('proof')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('payment_ref', 150)->nullable();
            $table->string('method_ref', 150)->nullable();
            $table->tinyInteger('type');
            $table->string('description', 250)->nullable();
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
        Schema::dropIfExists('payments');
    }
}
