<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditinvoicesAddCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->decimal('amount_due', 10, 2)->after('amount');
            $table->decimal('vat_amount', 10, 2)->after('amount_due');
            $table->decimal('net_of_vat', 10, 2)->after('vat_amount');
            $table->integer('no_of_person')->after('net_of_vat');
            $table->integer('no_of_scpwd')->after('no_of_person');
            $table->boolean('discounted')->after('no_of_scpwd');
            $table->decimal('discount_amount', 10, 2)->after('discounted');
            $table->decimal('discount_perc', 10, 2)->after('discount_amount');
            $table->decimal('net_sales', 10, 2)->after('discount_perc');
            $table->decimal('add_vat', 10, 2)->after('net_sales');
            $table->decimal('vat_perc', 10, 2)->after('add_vat');
            $table->integer('whtax_id')->after('vat_perc');
            $table->decimal('whtax_amount', 10, 2)->after('whtax_id');
            $table->string('terms', 64)->after('whtax_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creditinvoices', function(Blueprint $table){
            $table->dropColumn('amount_due');
            $table->dropColumn('vat_amount');
            $table->dropColumn('net_of_vat');
            $table->dropColumn('no_of_person');
            $table->dropColumn('no_of_scpwd');
            $table->dropColumn('discounted');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_perc');
            $table->dropColumn('net_sales');
            $table->dropColumn('add_vat');
            $table->dropColumn('vat_perc');
            $table->dropColumn('whtax_id');
            $table->dropColumn('whtax_amount');
            $table->dropColumn('terms');
        });
    }
}
