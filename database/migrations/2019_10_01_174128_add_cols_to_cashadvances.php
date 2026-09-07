<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsToCashadvances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashadvances', function (Blueprint $table) {
            $table->string('ca_deduction', 10, 2)->after('ca_amount');
            $table->string('keys')->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashadvances', function (Blueprint $table) {
            $table->dropColumn('ca_deduction');
            $table->dropColumn('keys');
        });
    }
}
