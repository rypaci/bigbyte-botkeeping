<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VouchersAddColDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function(Blueprint $table){
            $table->date('date')->after('ref_number');
        });

		// copy the dates from Invoice modules to Vouchers
		// need to do this in order to lessen the hassle in pulling voucher data specially in reports.
        $sql = "UPDATE supplier_invoices si LEFT JOIN vouchers v ON v.ref_id = si.id AND v.module_alias = 'si' SET v.date = si.date;";
        DB::update($sql);
        $sql = "UPDATE cash_payment_vouchers cpv LEFT JOIN vouchers v ON v.ref_id = cpv.id AND v.module_alias = 'cpv' SET v.date = cpv.date;";
        DB::update($sql);
        $sql = "UPDATE cashinvoices ci LEFT JOIN vouchers v ON v.ref_id = ci.id AND v.module_alias = 'ci' SET v.date = ci.date;";
        DB::update($sql);
        $sql = "UPDATE creditinvoices cri LEFT JOIN vouchers v ON v.ref_id = cri.id AND v.module_alias = 'cri' SET v.date = cri.date;";
        DB::update($sql);
        $sql = "UPDATE collection_receipts cr LEFT JOIN vouchers v ON v.ref_id = cr.id AND v.module_alias = 'cr' SET v.date = cr.date;";
        DB::update($sql);
        $sql = "UPDATE open_invoices oi LEFT JOIN vouchers v ON v.ref_id = oi.id AND v.module_alias = 'oi' SET v.date = oi.date;";
        DB::update($sql);
        $sql = "UPDATE billing_invoices bi LEFT JOIN vouchers v ON v.ref_id = bi.id AND v.module_alias = 'bi' SET v.date = bi.date;";
        DB::update($sql);
        $sql = "UPDATE official_receipts `or` LEFT JOIN vouchers v ON v.ref_id = `or`.id AND v.module_alias = 'or' SET v.date = `or`.date;";
        DB::update($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function(Blueprint $table){
            $table->dropColumn('date');
        });
    }
}
