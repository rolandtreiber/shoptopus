<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->json('background_image')->nullable();
            $table->boolean('show_button')->default(false);
            $table->text('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedBigInteger('total_clicks')->default(0);
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
        Schema::dropIfExists('banners');
    }
}
