<?php

use App\Enums\AccessTokenType;
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
            $table->uuid('id')->unique()->primary();
            $table->integer('type')->default(AccessTokenType::General);
            $table->string('token', 120);
            $table->uuidMorphs('accessable');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('issuer_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expiry')->nullable();
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
