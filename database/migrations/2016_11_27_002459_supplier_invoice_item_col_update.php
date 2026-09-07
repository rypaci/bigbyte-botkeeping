<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierInvoiceItemColUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoice_items', function (Blueprint $table) {
			$table->decimal('credit', 10, 2)->after('amount');
			$table->renameColumn('amount', 'debit');
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
			$table->dropColumn('credit');
			$table->renameColumn('debit', 'amount');
		});
    }
}
