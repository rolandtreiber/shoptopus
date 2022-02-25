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
            $table->foreignUuid('delivery_type_id')->constrained();
            $table->json('postcodes')->nullable();
            $table->unsignedBigInteger('min_weight')->nullable();
            $table->unsignedBigInteger('max_weight')->nullable();
            $table->unsignedBigInteger('min_distance')->nullable();
            $table->unsignedBigInteger('max_distance')->nullable();
            $table->string('distance_unit')->default('mile');
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
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
        Schema::dropIfExists('delivery_rules');
    }
}
