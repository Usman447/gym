<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgeToMstEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_enquiries', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('DOB');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_enquiries', function (Blueprint $table) {
            $table->dropColumn('age');
        });
    }
}
