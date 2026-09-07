<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('service_description');
            $table->text('rate');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop("services");
    }
}