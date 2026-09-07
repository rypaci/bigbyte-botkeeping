<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColVatExempt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashinvoices', function (Blueprint $table) {
            $table->decimal('vat_exempt', 10, 2)->after('net_of_vat');
        });
        Schema::table('creditinvoices', function (Blueprint $table) {
            $table->decimal('vat_exempt', 10, 2)->after('net_of_vat');
        });
        Schema::table('billing_invoices', function (Blueprint $table) {
            $table->decimal('vat_exempt', 10, 2)->after('net_of_vat');
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
            $table->dropColumn('vat_exempt');
        });
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->dropColumn('vat_exempt');
        });
        Schema::table('billing_invoices', function(Blueprint $table){
            $table->dropColumn('vat_exempt');
        });
    }
}
