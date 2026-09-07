<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDTRHoursshiftingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_t_r_hoursshiftings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('e_id');
            $table->string('start_time');
            $table->string('end_time');
            $table->string('date_shifting', 10);
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
        Schema::drop('d_t_r_hoursshiftings');
    }
}
