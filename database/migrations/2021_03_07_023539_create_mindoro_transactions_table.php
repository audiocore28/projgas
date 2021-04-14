<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMindoroTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mindoro_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('monthly_mindoro_transaction_id');
            $table->string('trip_no');
            $table->bigInteger('tanker_id');
            $table->bigInteger('driver_id');
            $table->bigInteger('helper_id');
            $table->decimal('expense', 10, 2)->nullable();
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
        Schema::dropIfExists('mindoro_transactions');
    }
}
