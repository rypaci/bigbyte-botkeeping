<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditinvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditinvoices', function(Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no', 128);
            $table->string('customer_id', 10);
            $table->date('credit_inv_date');
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
        Schema::drop('credit_invoices');
    }
}
