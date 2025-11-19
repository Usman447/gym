<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstFoodItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_food_items', function (Blueprint $table) {
            $table->increments('id')->comment('Unique Record Id for system');
            $table->string('name', 100)->comment('Name of the food item');
            $table->integer('amount')->comment('Price/amount of the food item');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_food_items_mst_users_1');
            $table->integer('updated_by')->unsigned()->nullable()->index('FK_mst_food_items_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_food_items');
    }
}
