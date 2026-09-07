<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashPaymentVouchersAddCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cash_payment_vouchers', function (Blueprint $table) {
            $table->string('invoice_number', 32)->after('check_number');
            $table->string('description')->after('invoice_number');
            $table->decimal('invoice_amount', 10, 2)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cash_payment_vouchers', function(Blueprint $table){
            $table->dropColumn('invoice_number');
            $table->dropColumn('description');
            $table->dropColumn('invoice_amount');
        });
    }
}
