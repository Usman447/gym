<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeleteFoodPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('permissions')->where('name', 'delete-food')->exists();
        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => 'delete-food',
                'display_name' => 'Delete food',
                'group_key' => 'Food',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'delete-food')->delete();
    }
}
