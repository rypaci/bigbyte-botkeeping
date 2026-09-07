<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashPaymentVouchersVersion2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_payment_vouchers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->integer('supplier_invoice_id');
            $table->string('cv_number', 16);
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('payment_method', 16);
            $table->boolean('on_hand');
            $table->boolean('bank');
            $table->string('bank_code', 32);
            $table->string('check_number', 32);
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
        Schema::drop('cash_payment_vouchers');
    }
}
