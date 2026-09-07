<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCashPaymentVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('cash_payment_vouchers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('cash_payment_vouchers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('business_name', 128);
            $table->text('business_address');
            $table->string('vat_reg_tin', 16);
            $table->string('business_cv_number', 16);
            $table->date('date');
            $table->string('pay_to', 128);
            $table->string('tin', 16);
            $table->text('address');
            $table->string('payment_method', 16);
            $table->string('check', 32);
            $table->string('bank_code', 16);
            $table->string('cv_number', 16);
            $table->decimal('invoice_amount', 10, 2);
            $table->timestamps();
        });
    }
}
