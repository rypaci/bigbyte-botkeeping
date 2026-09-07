<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashPaymentVoucherItemsVersion2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_payment_voucher_items', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('cash_payment_voucher_id');
            $table->integer('chart_account_id');
            $table->string('invoice_number', 16);
            $table->decimal('debit', 10, 2);
            $table->decimal('credit', 10, 2);
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
        Schema::drop('cash_payment_voucher_items');
    }
}
