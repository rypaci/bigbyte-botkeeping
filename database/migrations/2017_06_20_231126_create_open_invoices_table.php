<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOpenInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_invoices', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('credit_invoice_id');
            $table->string('oi_number', 16);
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->decimal('sales_discount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('payment_method', 16);
            $table->boolean('on_hand');
            $table->boolean('bank');
            $table->boolean('bank_code', 32);
            $table->string('check_number', 32);
            $table->string('invoice_number', 32);
            $table->string('description');
            $table->decimal('invoice_amount', 10, 2);
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
        Schema::drop('open_invoices');
    }
}
