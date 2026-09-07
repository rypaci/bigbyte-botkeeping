<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('individual');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('business_name');
            $table->text('business_address');
            $table->string('city');
            $table->string('country');
            $table->string('tin');
            $table->string('branch_code');
            $table->string('phone_no');
            $table->string('fax');
            $table->string('email');
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
        Schema::drop("customers");
    }
}
