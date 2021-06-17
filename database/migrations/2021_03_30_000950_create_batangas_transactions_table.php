<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatangasTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batangas_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monthly_batangas_transaction_id');
            $table->string('trip_no');
            $table->bigInteger('tanker_id');
            $table->bigInteger('driver_id');
            $table->bigInteger('helper_id');
            $table->decimal('driver_salary', 8, 0)->nullable();
            $table->decimal('helper_salary', 8, 0)->nullable();
            $table->timestamps();

            $table->foreign('monthly_batangas_transaction_id')->references('id')->on('monthly_batangas_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batangas_transactions');
    }
}
