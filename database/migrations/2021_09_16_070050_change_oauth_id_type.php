<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->uuid('user_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};
