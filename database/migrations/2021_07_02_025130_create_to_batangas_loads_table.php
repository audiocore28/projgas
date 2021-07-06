<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToBatangasLoadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_batangas_loads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batangas_transaction_id')->nullable();
            $table->unsignedBigInteger('purchase_id');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('batangas_transaction_id')->references('id')->on('batangas_transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_batangas_loads');
    }
}
