<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditinvoicesAddCol3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditinvoices', function (Blueprint $table) {
            $table->decimal('advance_payment', 10, 2)->after('open_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->dropColumn('advance_payment');
        });
    }
}
