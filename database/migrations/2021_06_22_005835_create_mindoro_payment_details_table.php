<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMindoroPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mindoro_payment_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('mode')->nullable;
            $table->decimal('amount', 10, 2);
            $table->longText('remarks')->nullable();
            $table->bigInteger('mindoro_transaction_detail_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mindoro_payment_details');
    }
}
