<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCashadvancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashadvances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('e_id');
            $table->decimal('ca_amount', 10, 2);
            $table->string('ca_description');
            $table->string('date');
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
        Schema::drop('cashadvances');
    }
}