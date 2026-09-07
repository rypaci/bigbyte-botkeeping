<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashinvoicesUpdateCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashinvoices', function(Blueprint $table){
            $table->integer('discount_id')->after('discounted');
            $table->integer('vat_id')->after('net_sales');
            $table->decimal('vatable_sales', 10, 2)->after('vat_amount');
            $table->decimal('vat_exempt_sales', 10, 2)->after('vatable_sales');
            $table->dropColumn('add_vat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashinvoices', function(Blueprint $table){
            $table->dropColumn(['discount_id', 'vat_id', 'vatable_sales', 'vat_exempt_sales']);
            $table->decimal('add_vat', 10 ,2)->after('net_sales');
        });
    }
}
