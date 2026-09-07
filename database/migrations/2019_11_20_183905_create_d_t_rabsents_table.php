<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDTRabsentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_t_rabsents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('e_id');
            $table->string('date', 10);
            $table->string('absent_no', 3);
            $table->integer('user_account_id');
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
        Schema::drop('d_t_rabsents');
    }
}
