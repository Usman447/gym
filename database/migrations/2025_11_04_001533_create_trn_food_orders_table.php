<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnFoodOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_food_orders', function (Blueprint $table) {
            $table->increments('id')->comment('Unique Record Id for system');
            $table->string('order_number', 50)->comment('Order number (ORD1, ORD2, etc.)');
            $table->integer('total_amount')->comment('Total amount of the food order');
            $table->integer('payment_mode')->default(1)->comment('0 = Cheque, 1 = Cash, 2 = Online');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_food_orders_mst_users_1');
            $table->integer('updated_by')->unsigned()->nullable()->index('FK_trn_food_orders_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_food_orders');
    }
}
