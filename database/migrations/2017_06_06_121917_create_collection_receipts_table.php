<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCollectionReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_receipts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('credit_invoice_id');
            $table->string('cr_number', 16);
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 16);
            $table->boolean('on_hand');
            $table->boolean('bank');
            $table->string('bank_code', 32);
            $table->string('check_number', 32);
            $table->decimal('balance', 10, 2);
            $table->string('invoice_number');
            $table->decimal('invoice_amount', 10, 2);
            $table->string('description');
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
        Schema::drop('collection_receipts');
    }
}
