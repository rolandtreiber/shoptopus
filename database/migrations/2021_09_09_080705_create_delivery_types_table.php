<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->json('name');
            $table->json('description');
            $table->decimal('price')->default(0);
            $table->string('slug');
            $table->boolean('enabled')->default(true);
            $table->boolean('enabled_by_default_on_creation')->default(true);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('delivery_types');
        Schema::enableForeignKeyConstraints();
    }
}
