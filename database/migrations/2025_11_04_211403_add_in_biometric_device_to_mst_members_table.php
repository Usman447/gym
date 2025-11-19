<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInBiometricDeviceToMstMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_members', function (Blueprint $table) {
            $table->boolean('in_biometric_device')->default(false)->after('opf_residence');
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
            $table->dropColumn('in_biometric_device');
        });
    }
}
