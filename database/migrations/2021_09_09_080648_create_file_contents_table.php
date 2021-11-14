<?php

use App\Enums\FileTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('file_contents', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('url');
            $table->string('file_name');
            $table->string('fileable_type')->nullable();
            $table->uuid('fileable_id')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('type')->default(FileTypes::Image);
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
        Schema::dropIfExists('file_contents');
    }
}
