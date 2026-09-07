<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function(Blueprint $table) {
            $table->increments('id');
            $table->string('last_name', 128);
            $table->string('first_name', 128);
            $table->string('middle_name', 128);
            $table->string('gender', 32);
            $table->string('civil_status', 16);
            $table->string('spouse');
            $table->string('business_name');
            $table->string('business_id');
            $table->text('business_address');
            $table->string('city');
            $table->string('country');
            $table->string('zip', 64);
            $table->string('business_type');
            $table->string('tin', 64);
            $table->string('branch_code', 32);
            $table->string('rdo_code', 16);
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
        Schema::drop('companies');
    }
}
