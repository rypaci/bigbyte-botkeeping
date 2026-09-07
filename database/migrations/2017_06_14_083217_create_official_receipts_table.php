<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOfficialReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('official_receipts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id');
            $table->integer('billing_invoice_id');
            $table->string('or_number', 16);
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('payment_method', 16);
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
        Schema::drop('official_receipts');
    }
}
