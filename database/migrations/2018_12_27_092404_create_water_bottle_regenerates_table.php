<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterBottleRegeneratesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_bottle_regenerates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_qty');
            $table->integer('refill_bottle');
            $table->integer('container_qty');
            $table->integer('dealer_qty');
            $table->integer('others_qty');
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
        //
    }
}
