<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDeleteEnquiryPermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permission = [
            'name' => 'delete-enquiry',
            'display_name' => 'Delete enquiry',
            'group_key' => 'Enquiries',
        ];

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'delete-enquiry')->delete();
    }
}
