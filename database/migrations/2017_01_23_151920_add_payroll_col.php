<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayrollCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('salary_days_present', 128)->after('e_id');
            $table->string('salary_rate', 128)->after('salary_days_present');
            $table->string('salary_total', 128)->after('salary_rate');
            $table->string('overtime', 128)->after('salary_total');
            $table->string('overtime_rate', 128)->after('overtime');
            $table->string('overtime_total', 128)->after('overtime_rate');
            $table->string('cola', 128)->after('overtime_total');
            $table->string('total', 128)->after('cola');
            $table->string('emp_con_sss', 128)->after('total');
            $table->string('emp_con_phic', 128)->after('emp_con_sss');
            $table->string('emp_con_pagibig', 128)->after('emp_con_phic');
            $table->string('late', 128)->after('emp_con_pagibig');
            $table->string('late_rate', 128)->after('late');
            $table->string('late_total', 128)->after('late_rate');
            $table->string('employer_con_sss', 128)->after('late_total');
            $table->string('employer_con_phic', 128)->after('employer_con_sss');
            $table->string('employer_con_pagibig', 128)->after('employer_con_phic');
            $table->string('net_pay', 128)->after('employer_con_pagibig');
            $table->string('tax_withheld', 128)->after('net_pay');
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
