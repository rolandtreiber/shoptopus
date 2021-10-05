<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::connection(config('app.env') === "testing" ? 'sqlite_logs' : 'logs')->create('audits', function (Blueprint $table) {
            $primaryDbName = DB::connection(config('app.env') === "testing" ? 'sqlite' : 'mysql')->getDatabaseName();

            $table->bigIncrements('id');
            $table->string('user_type')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('event');
            $table->uuidMorphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            config('app.env') !== "testing" && $table->foreign('user_id')->references('id')->on($primaryDbName . '.users')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'user_type']);
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
        Schema::connection(config('app.env') === "testing" ? 'sqlite_logs' : 'logs')->dropIfExists('audits');
    }
}
