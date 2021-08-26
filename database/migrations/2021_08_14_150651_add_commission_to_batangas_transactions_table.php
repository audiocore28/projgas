<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionToBatangasTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batangas_transactions', function (Blueprint $table) {
            $table->decimal('commission', 8, 0)->nullable()->after('helper_salary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batangas_transactions', function (Blueprint $table) {
            $table->dropColumn('commission');
        });
    }
}
