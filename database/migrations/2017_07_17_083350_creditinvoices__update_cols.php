<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditinvoicesUpdateCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->decimal('vatable_sales', 10, 2)->after('vat_amount');
            $table->decimal('vat_exempt_sales', 10, 2)->after('vatable_sales');
            $table->integer('discount_id')->after('discounted');
            $table->integer('vat_id')->after('net_sales');
            $table->dropColumn('add_vat');
            $table->decimal('debit_total', 10, 2)->after('whtax_amount');
            $table->decimal('credit_total', 10, 2)->after('debit_total');
        });
        
        $sql  = "update creditinvoices as cri set ";
        $sql .= "debit_total = (select sum(debit) from vouchers where ref_id = cri.id and module_alias = 'cri'), ";
        $sql .= "credit_total = (select sum(credit) from vouchers where ref_id = cri.id and module_alias = 'cri')";
        DB::update($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->dropColumn(['discount_id', 'vat_id', 'vatable_sales', 'vat_exempt_sales']);
            $table->dropColumn(['debit_total', 'credit_total']);
            $table->decimal('add_vat', 10 ,2)->after('net_sales');
        });
    }
}
