<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChartAccountsColSubAccountTypeId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->integer('sub_account_type_id')->after('account_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->dropColumn('sub_account_type_id');
        });
    }
}
