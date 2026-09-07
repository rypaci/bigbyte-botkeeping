<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfficialReceiptsAddCol2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('official_receipts', function (Blueprint $table) {
            $table->boolean('on_hand')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('official_receipts', function(Blueprint $table){
            $table->dropColumn('on_hand');
        });
    }
}
