<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_invoices', function(Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no', 128);
            $table->string('customer_id', 10);
            $table->date('cash_inv_date');
            $table->string('pay_to', 128);
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
        Schema::drop('cash_invoices');
    }
}
