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
            $table->date('date')->nullable();
            $table->string('mode')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->longText('remarks')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('mindoro_transaction_detail_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mindoro_transaction_detail_id')->references('id')->on('mindoro_transaction_details')->onDelete('cascade');
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
