<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChartAccountsAddColLevel1Parent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chart_accounts', function(Blueprint $table){
            $table->integer('level1_parent')->after('parent_account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chart_accounts', function(Blueprint $table){
            $table->dropColumn('level1_parent');
        });
    }
}
