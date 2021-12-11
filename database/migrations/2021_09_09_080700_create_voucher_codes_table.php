<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->tinyInteger('type');
            $table->decimal('amount');
            $table->string('code', 100);
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
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
        Schema::dropIfExists('voucher_codes');
    }
}
