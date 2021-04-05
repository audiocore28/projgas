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
            $table->unsignedBigInteger('station_transaction_id');
            $table->string('voucher_no')->nullable();
            $table->decimal('cash', 10, 3)->nullable();
            $table->bigInteger('client_id');
            $table->decimal('quantity', 8, 3)->nullable();
            $table->decimal('disc_amount', 8, 3)->nullable();
            $table->timestamps();

            $table->foreign('station_transaction_id')->references('id')->on('station_transactions')->onDelete('cascade');
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
