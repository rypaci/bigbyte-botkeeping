<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsSupplierInvoiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            $table->integer('tax_id')->after('chart_account_id');
            $table->decimal('rate', 10, 2)->after('tax_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
            $table->dropColumn('tax_id');
            $table->dropColumn('rate');
        });
    }
}
