<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OfficialReceiptsAddCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('official_receipts', function (Blueprint $table) {
            $table->decimal('sales_discount', 10, 2)->after('amount');
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
            $table->dropColumn('sales_discount');
        });
    }
}
