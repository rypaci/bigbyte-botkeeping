<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWRMExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_r_m_expenses', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('vendor_id');
            $table->string('invoice_no');
            $table->string('terms');
            $table->string('period');
            $table->decimal('amount', 10, 2);
            $table->string('description');
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
        Schema::drop('w_r_m_expenses');
    }
}
