<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntryUserTrackingToMstMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->string('created_by_user_name', 255)->nullable()->after('created_by');
            $table->string('created_by_user_email', 255)->nullable()->after('created_by_user_name');
            $table->string('updated_by_user_name', 255)->nullable()->after('updated_by');
            $table->string('updated_by_user_email', 255)->nullable()->after('updated_by_user_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->dropColumn(['created_by_user_name', 'created_by_user_email', 'updated_by_user_name', 'updated_by_user_email']);
        });
    }
}

