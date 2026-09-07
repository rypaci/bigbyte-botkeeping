<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CollectionReceiptsAddCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collection_receipts', function (Blueprint $table) {
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
        Schema::table('collection_receipts', function(Blueprint $table){
            $table->dropColumn('sales_discount');
        });
    }
}
