<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
			$table->string('lastname');
			$table->string('firstname');
			$table->string('middlename');
			$table->date('dob');
			$table->string('gender', 6);
			$table->text('address');
			$table->string('country');
			$table->string('phone');
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
