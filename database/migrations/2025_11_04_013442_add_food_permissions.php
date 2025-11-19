<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFoodPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            [
                'name' => 'manage-food',
                'display_name' => 'Manage food',
                'group_key' => 'Food',
            ],
            [
                'name' => 'order-food',
                'display_name' => 'Order food',
                'group_key' => 'Food',
            ],
            [
                'name' => 'add-food-items',
                'display_name' => 'Add food items',
                'group_key' => 'Food',
            ],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'],
                    'group_key' => $permission['group_key'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->whereIn('name', ['manage-food', 'order-food', 'add-food-items'])->delete();
    }
}
