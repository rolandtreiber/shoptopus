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
        Schema::disableForeignKeyConstraints();

        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('status')->default(1);
            $table->boolean('enabled_by_default_on_creation')->default(true);
            $table->decimal('price')->default(0);
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
        Schema::dropIfExists('delivery_types');
    }
}
