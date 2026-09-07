<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_name', 128)->after('spouse');
            $table->dateTime('registration_date')->after('company_name');
            $table->string('registration_number', 32)->after('registration_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->dropColumn('registration_date');
            $table->dropColumn('registration_number');
        });
    }
}
