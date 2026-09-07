<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashinvoicesRenameCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashinvoices', function (Blueprint $table) {
            $table->renameColumn('invoice_no', 'invoice_number');
            $table->renameColumn('cash_inv_date', 'date');
            $table->renameColumn('pay_to', 'customer_name');
            $table->renameColumn('invoice_amount', 'amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashinvoices', function (Blueprint $table) {
            $table->renameColumn('invoice_number', 'invoice_no');
            $table->renameColumn('date', 'cash_inv_date');
            $table->renameColumn('customer_name', 'pay_to');
            $table->renameColumn('amount', 'invoice_amount');
        });
    }
}
