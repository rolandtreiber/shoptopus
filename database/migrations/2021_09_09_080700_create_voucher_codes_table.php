<?php

use App\Enums\DiscountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('voucher_codes', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->decimal('amount');
            $table->string('code', 100);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->unsignedInteger('type')->default(DiscountType::Percentage);
            $table->boolean('enabled')->default(true);
            $table->string('slug');
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
        Schema::dropIfExists('voucher_codes');
    }
}
