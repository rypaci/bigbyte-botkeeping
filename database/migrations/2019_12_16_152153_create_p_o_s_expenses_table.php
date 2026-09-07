<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePOSExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_o_s_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vendor_id');
            $table->string('invoice_no');
            $table->string('terms');
            $table->string('period');
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->string('remarks', 1);
            $table->date('date');
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
        Schema::drop('p_o_s_expenses');
    }
}
