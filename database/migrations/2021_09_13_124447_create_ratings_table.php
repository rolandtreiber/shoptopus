<?php

use App\Facades\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Module::enabled('ratings') && Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->string('ratable_type')->nullable();
            $table->uuid('ratable_id')->nullable();
            $table->integer('rating');
            $table->string('language_prefix')->nullable();
            $table->string('description')->nullable();
            $table->string('title')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('verified')->default(true);
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
        Module::enabled('ratings') && Schema::dropIfExists('ratings');
    }
}
