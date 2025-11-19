<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_inventory', function (Blueprint $table) {
            $table->increments('id')->comment('Unique Record Id for system');
            $table->string('name', 100)->comment('Name of the inventory item');
            $table->integer('amount')->comment('Price/amount of the inventory item');
            $table->integer('quantity')->comment('Quantity available in inventory');
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index('FK_mst_inventory_mst_users_1');
            $table->integer('updated_by')->unsigned()->nullable()->index('FK_mst_inventory_mst_users_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mst_inventory');
    }
}
