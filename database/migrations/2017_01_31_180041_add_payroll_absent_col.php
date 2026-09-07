<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayrollAbsentCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('days_absent')->after('employer_con_pagibig');
            $table->string('absent_rate')->after('days_absent');
            $table->string('absent_total')->after('absent_rate');
            $table->string('salary_method')->after('absent_total');
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
