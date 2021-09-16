<?php

use App\Models\PaymentSource;
use App\Models\User;
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
            $table->string('payable_type')->nullable();
            $table->unsignedBigInteger('payable_id')->nullable();
            $table->foreignIdFor(PaymentSource::class, 'payment_source_id');
            $table->foreignIdFor(User::class, 'user_id');
            $table->decimal('amount');
            $table->tinyInteger('status')->default(0);
            $table->string('payment_ref', 150);
            $table->string('method_ref', 150);
            $table->tinyInteger('type');
            $table->string('description', 250)->nullable();
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
