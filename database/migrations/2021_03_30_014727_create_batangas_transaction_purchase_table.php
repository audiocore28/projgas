<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatangasTransactionPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batangas_transaction_purchase', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batangas_transaction_id');
            $table->unsignedBigInteger('purchase_id');
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
        Schema::dropIfExists('batangas_transaction_purchase');
    }
}
