<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChartAccountsAddCols2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_accounts', function(Blueprint $table){
            $table->boolean('si_asset')->after('normal_balance');
            $table->boolean('si_asset_debit')->after('si_asset');
            $table->boolean('si_asset_credit')->after('si_asset_debit');
            $table->boolean('si_purchases')->after('si_asset_credit');
            $table->boolean('si_purchases_debit')->after('si_purchases');
            $table->boolean('si_purchases_credit')->after('si_purchases_debit');
            $table->boolean('si_accruals')->after('si_purchases_credit');
            $table->boolean('si_accruals_debit')->after('si_accruals');
            $table->boolean('si_accruals_credit')->after('si_accruals_debit');
            $table->boolean('cpv_asset')->after('si_accruals_credit');
            $table->boolean('cpv_asset_debit')->after('cpv_asset');
            $table->boolean('cpv_asset_credit')->after('cpv_asset_debit');
            $table->boolean('cpv_purchases')->after('cpv_asset_credit');
            $table->boolean('cpv_purchases_debit')->after('cpv_purchases');
            $table->boolean('cpv_purchases_credit')->after('cpv_purchases_debit');
            $table->boolean('cpv_accruals')->after('cpv_purchases_credit');
            $table->boolean('cpv_accruals_debit')->after('cpv_accruals');
            $table->boolean('cpv_accruals_credit')->after('cpv_accruals_debit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_accounts', function(Blueprint $table) {
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
            $table->dropColumn('cpv_asset_debit');
            $table->dropColumn('cpv_asset_credit');
            $table->dropColumn('cpv_purchases');
            $table->dropColumn('cpv_purchases_debit');
            $table->dropColumn('cpv_purchases_credit');
            $table->dropColumn('cpv_accruals');
            $table->dropColumn('cpv_accruals_debit');
            $table->dropColumn('cpv_accruals_credit');
        });
    }
}
