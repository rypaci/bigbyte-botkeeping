<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosExpenseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('pos_expense_items')) {
            Schema::create('pos_expense_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('pos_expense_id');
                $table->unsignedInteger('product_id');
                $table->integer('quantity')->default(1);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->decimal('total_price', 10, 2)->default(0);
                $table->text('remarks')->nullable();
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
        Schema::drop('pos_expense_items');
    }
}
