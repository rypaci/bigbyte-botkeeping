<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWRMChangesapfiltercountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('w_r_m_changesapfiltercounters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_qty');
            $table->integer('refill_bottle');
            $table->integer('container_qty');
            $table->integer('dealer_qty');
            $table->integer('others_qty');
            $table->string('type_filter');
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
        Schema::drop('w_r_m_changesapfiltercounters');
    }
}
