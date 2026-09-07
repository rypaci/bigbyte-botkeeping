<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeCompnameToPossales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('p_o_s_sales', function (Blueprint $table) {
            $table->string('emp_id', 6)->after('status');
            $table->string('comp_name', 50)->after('emp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('p_o_s_sales', function (Blueprint $table) {
            $table->dropColumn('emp_id');
            $table->dropColumn('comp_name');
        });
    }
}
