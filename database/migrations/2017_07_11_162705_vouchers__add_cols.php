<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VouchersAddCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vouchers', function(Blueprint $table){
            $table->integer('discount_id')->after('tax_id');
            $table->decimal('rate', 10, 2)->after('discount_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vouchers', function(Blueprint $table){
            $table->dropColumn(['rate', 'discount_id']);
        });
    }
}
