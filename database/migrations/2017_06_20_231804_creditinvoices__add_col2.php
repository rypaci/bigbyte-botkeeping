<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditinvoicesAddCol2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditinvoices', function (Blueprint $table) {
            $table->boolean('for_open_invoice')->after('status');
            $table->integer('open_invoice_id')->after('for_open_invoice');
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
            $table->dropColumn('for_open_invoice');
            $table->dropColumn('open_invoice_id');
        });
    }
}
