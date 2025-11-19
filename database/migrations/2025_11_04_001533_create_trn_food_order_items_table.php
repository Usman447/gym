<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrnFoodOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_food_order_items', function (Blueprint $table) {
            $table->increments('id')->comment('Unique Record Id for system');
            $table->integer('food_order_id')->unsigned()->index('FK_trn_food_order_items_trn_food_orders_1')->comment('links to unique record id of trn_food_orders');
            $table->integer('food_item_id')->unsigned()->index('FK_trn_food_order_items_mst_food_items_1')->comment('links to unique record id of mst_food_items');
            $table->integer('quantity')->default(1)->comment('Quantity of the food item');
            $table->integer('item_amount')->comment('Amount of the item at the time of order');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_trn_food_order_items_mst_users_1');
            $table->integer('updated_by')->unsigned()->nullable()->index('FK_trn_food_order_items_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_food_order_items');
    }
}
