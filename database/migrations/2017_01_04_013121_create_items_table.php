<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{

    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name');
            $table->text('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop("items");
    }
}