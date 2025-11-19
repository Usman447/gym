<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddInventorySupportToFoodOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Make food_item_id nullable to support inventory items
        DB::statement('ALTER TABLE `trn_food_order_items` MODIFY `food_item_id` INT(10) UNSIGNED NULL');
        
        // Add inventory_id field
        Schema::table('trn_food_order_items', function (Blueprint $table) {
            $table->integer('inventory_id')->unsigned()->nullable()->index('FK_trn_food_order_items_mst_inventory_1')->comment('links to unique record id of mst_inventory')->after('food_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_food_order_items', function (Blueprint $table) {
            $table->dropColumn('inventory_id');
        });
        
        // Revert food_item_id to not nullable
        DB::statement('ALTER TABLE `trn_food_order_items` MODIFY `food_item_id` INT(10) UNSIGNED NOT NULL');
    }
}
