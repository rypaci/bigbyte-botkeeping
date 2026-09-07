<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePOSSoldstocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('p_o_s_soldstocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sales_id', 10);
            $table->string('product_id', 10);
            $table->string('qty', 10);
            $table->decimal('price', 10, 2);
            $table->decimal('amount', 10, 2);
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
        Schema::drop('p_o_s_soldstocks');
    }
}
