<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoices', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->date('date');
            $table->string('invoice_number', 32);
            $table->string('terms', 32);
            $table->decimal('vat', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_due', 10, 2);
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
        Schema::drop('supplier_invoices');
    }
}
