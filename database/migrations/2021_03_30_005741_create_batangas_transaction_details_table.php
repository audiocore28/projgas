<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatangasTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batangas_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batangas_transaction_id');
            $table->date('date');
            $table->bigInteger('client_id');
            $table->bigInteger('product_id');
            $table->decimal('quantity', 8, 0)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('batangas_transaction_id')->references('id')->on('batangas_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batangas_transaction_details');
    }
}
