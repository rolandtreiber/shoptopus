<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('discount_rules', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->tinyInteger('type');
            $table->text('name');
            $table->unsignedDecimal('amount');
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->boolean('enabled')->default(true);
            $table->string('slug');
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
        Schema::dropIfExists('discount_rules');
    }
}
