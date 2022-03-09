<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('delivery_rules', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('delivery_type_id')->nullable()->constrained();
            $table->json('postcodes')->nullable();
            $table->decimal('min_weight')->nullable();
            $table->decimal('max_weight')->nullable();
            $table->decimal('min_distance')->nullable();
            $table->decimal('max_distance')->nullable();
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('delivery_rules');
    }
}
