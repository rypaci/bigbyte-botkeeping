<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePOSSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_o_s_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_id', 10);
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amt_balance', 10, 2);
            $table->date('sales_date');
            $table->string('status', 8);
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
        Schema::drop('p_o_s_sales');
    }
}
