<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierInvoiceColUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
			$table->decimal('vat_perc', 10, 2)->after('terms');
			$table->renameColumn('vat', 'vat_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('supplier_invoices', function (Blueprint $table) {
			$table->dropColumn('vat_perc');
			$table->renameColumn('vat_amount', 'vat');
		});
    }
}
