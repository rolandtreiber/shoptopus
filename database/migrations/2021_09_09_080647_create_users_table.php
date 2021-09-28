<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
//            $table->id();
//            $table->uuid('uuid')->unique();
            $table->string('name', 100);
            $table->tinyInteger('role_id')->default(0);
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at');
            $table->string('password');
            $table->string('client_ref', 12)->nullable()->index();
            $table->boolean('temporary')->default(false);
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
        Schema::dropIfExists('users');
    }
}
