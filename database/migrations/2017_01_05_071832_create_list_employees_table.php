<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_name');
            $table->string('tin_no');
            $table->string('sex');
            $table->string('status');
            $table->string('dependents');
            $table->string('daily_rate');
            $table->string('monthly_rate');
            $table->string('overtime_rate');
            $table->string('late_rate');
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
        Schema::drop("employees");
    }
}
