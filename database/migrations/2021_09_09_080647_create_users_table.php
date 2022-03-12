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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->string('name', 100)->nullable();
            $table->string('initials', 5)->nullable();
            $table->string('prefix', 5)->nullable();
            $table->string('phone', 50)->nullable();
            $table->json('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('client_ref', 12)->nullable()->index();
            $table->boolean('temporary')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->string('slug');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
}
