<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTrnAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('trn_attendance')) {
            Schema::create('trn_attendance', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('member_id')->unsigned();
                $table->datetime('check_in_time');
                $table->timestamps();
                $table->integer('created_by')->unsigned()->nullable();
                $table->integer('updated_by')->unsigned()->nullable();

                $table->index('member_id');
                $table->index('check_in_time');
                $table->index('created_at');
            });
        }
        
        // Add foreign key using raw SQL to match member table structure (if not exists)
        try {
            DB::statement('ALTER TABLE trn_attendance ADD CONSTRAINT trn_attendance_member_id_foreign FOREIGN KEY (member_id) REFERENCES mst_members(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trn_attendance');
    }
}
