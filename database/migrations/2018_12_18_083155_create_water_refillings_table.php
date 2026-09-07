<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterRefillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_refillings', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('pro_id');
            $table->string('entry_no',10);
            $table->integer('customer_id');
            $table->integer('return_bottle');
            $table->integer('order_qty');
            $table->integer('container_qty');
            $table->integer('dealer_qty');
            $table->decimal('amount_due', 10, 2);
            $table->date('date');
            $table->string('status');
            $table->decimal('amt_balance');
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
        Schema::drop('water_refillings');
    }
}
