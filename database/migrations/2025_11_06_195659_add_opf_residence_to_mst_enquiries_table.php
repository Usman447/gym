<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpfResidenceToMstEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_enquiries', function (Blueprint $table) {
            $table->boolean('opf_residence')->nullable()->after('address');
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
            $table->dropColumn('opf_residence');
        });
    }
}
