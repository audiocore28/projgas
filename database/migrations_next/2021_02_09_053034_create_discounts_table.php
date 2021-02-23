<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->nullable();
            $table->decimal('cash', 10, 3)->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->decimal('quantity', 8, 3)->nullable();
            $table->decimal('disc_amount', 8, 3)->nullable();
            $table->bigInteger('station_transaction_id');
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
        Schema::dropIfExists('discounts');
    }
}
