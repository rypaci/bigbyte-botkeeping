<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function(Blueprint $table) {
            $table->increments('id');
            $table->string('last_name', 128);
            $table->string('first_name', 128);
            $table->string('middle_name', 128);
            $table->string('business_name');
            $table->text('business_address');
            $table->string('city');
            $table->string('country');
            $table->string('tin', 64);
            $table->string('branch_code');
            $table->decimal('opening_balance', 10, 2);
            $table->date('as_of');
            $table->string('phone_number', 32);
            $table->string('fax', 32);
            $table->string('email', 128);
            $table->tinyInteger('individual');
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
        Schema::drop('vendors');
    }
}
