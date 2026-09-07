<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemarksToDTRabsent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('d_t_rabsents', function (Blueprint $table) {
            $table->string('remarks')->after('absent_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('d_t_rabsents', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
}
