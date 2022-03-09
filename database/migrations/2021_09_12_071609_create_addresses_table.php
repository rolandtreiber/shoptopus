<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('address_line_1');
            $table->string('town');
            $table->string('post_code');
            $table->string('country')->default('UK');
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->string('name')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
