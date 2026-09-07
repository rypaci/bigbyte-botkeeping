<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChartAccountsDropCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_accounts', function(Blueprint $table){
            $table->dropColumn('si_asset');
            $table->dropColumn('si_asset_debit');
            $table->dropColumn('si_asset_credit');
            $table->dropColumn('si_purchases');
            $table->dropColumn('si_purchases_debit');
            $table->dropColumn('si_purchases_credit');
            $table->dropColumn('si_accruals');
            $table->dropColumn('si_accruals_debit');
            $table->dropColumn('si_accruals_credit');
            $table->dropColumn('cpv_asset');
            $table->dropColumn('cpv_asset_credit');
            $table->dropColumn('cpv_asset_debit');
            $table->dropColumn('cpv_purchases');
            $table->dropColumn('cpv_purchases_debit');
            $table->dropColumn('cpv_purchases_credit');
            $table->dropColumn('cpv_accruals');
            $table->dropColumn('cpv_accruals_debit');
            $table->dropColumn('cpv_accruals_credit');
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
