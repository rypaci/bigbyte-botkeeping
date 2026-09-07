<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CashinvoicesAddCols2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashinvoices', function(Blueprint $table){
            $table->decimal('debit_total', 10, 2)->after('whtax_amount');
            $table->decimal('credit_total', 10, 2)->after('debit_total');
        });
        
        $sql  = "update cashinvoices as ci set ";
        $sql .= "debit_total = (select sum(debit) from vouchers where ref_id = ci.id and module_alias = 'ci'), ";
        $sql .= "credit_total = (select sum(credit) from vouchers where ref_id = ci.id and module_alias = 'ci')";
        DB::update($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashinvoices', function(Blueprint $table){
            $table->dropColumn('debit_total');
            $table->dropColumn('credit_total');
        });
    }
}
