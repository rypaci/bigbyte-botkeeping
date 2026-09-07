<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderItemsAddmodCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->renameColumn('cashinvoice_id', 'ref_id');
            $table->string('module_alias', 16)->after('cashinvoice_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function(Blueprint $table){
            $table->renameColumn('ref_id', 'cashinvoice_id');
            $table->dropColumn('module_alias');
        });
    }
}
