<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DiscountsUpdateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->renameColumn('discount', 'name');
            $table->decimal('rate', 10, 2)->change();
            $table->integer('chart_account_id')->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->renameColumn('name', 'discount');
            $table->string('rate')->change();
            $table->dropColumn('chart_account_id');
        });
    }
}
