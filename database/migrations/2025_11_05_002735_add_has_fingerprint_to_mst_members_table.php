<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasFingerprintToMstMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->boolean('has_fingerprint')->default(false)->after('in_biometric_device');
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
            $table->dropColumn('has_fingerprint');
        });
    }
}
