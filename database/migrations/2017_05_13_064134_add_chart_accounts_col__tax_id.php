<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChartAccountsColTaxId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_accounts', function (Blueprint $table) {
            $table->integer('tax_id')->after('normal_balance');
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
            $table->dropColumn('tax_id');
        });
    }
}
