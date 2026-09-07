<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBillingInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_invoices', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->string('invoice_number', 128);
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_due', 10, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('net_of_vat', 10, 2);
            $table->integer('no_of_person');
            $table->integer('no_of_scpwd');
            $table->boolean('discounted');
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('discount_perc', 10, 2);
            $table->decimal('net_sales', 10, 2);
            $table->decimal('add_vat', 10, 2);
            $table->decimal('vat_perc', 10, 2);
            $table->decimal('whtax_id', 10, 2);
            $table->decimal('whtax_amount', 10, 2);
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
        Schema::drop('billing_invoices');
    }
}
