<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('product_batches')) {
            Schema::create('product_batches', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('product_id')->index();
                $table->unsignedInteger('inventory_id')->nullable();
                $table->string('batch_code', 50);
                $table->integer('original_qty')->default(0);
                $table->date('expiry_date')->nullable();
                $table->date('mfg_date')->nullable();
                $table->date('received_date');
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('product_batches');
    }
}
