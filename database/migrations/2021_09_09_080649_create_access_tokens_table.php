<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('access_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tinyInteger')->default('0');
            $table->string('token', 120);
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(User::class, 'issuer_user_id');
            $table->dateTime('expiry');
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
        Schema::dropIfExists('access_tokens');
    }
}
