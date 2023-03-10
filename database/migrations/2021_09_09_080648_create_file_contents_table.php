<?php

use App\Enums\FileType;
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
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('file_contents', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('url');
            $table->string('file_name');
            $table->uuidMorphs('fileable');
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('type')->default(FileType::Image);
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
};
