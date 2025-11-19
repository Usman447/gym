<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTimingsValuesInDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update mst_members table
        // Evening -> Ladies
        DB::table('mst_members')
            ->where('timings', 'Evening')
            ->update(['timings' => 'Ladies']);
        
        // Night -> Evening
        DB::table('mst_members')
            ->where('timings', 'Night')
            ->update(['timings' => 'Evening']);
        
        // Update mst_users table
        // Evening -> Ladies
        DB::table('mst_users')
            ->where('timings', 'Evening')
            ->update(['timings' => 'Ladies']);
        
        // Night -> Evening
        DB::table('mst_users')
            ->where('timings', 'Night')
            ->update(['timings' => 'Evening']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse: Ladies -> Evening
        DB::table('mst_members')
            ->where('timings', 'Ladies')
            ->update(['timings' => 'Evening']);
        
        // Reverse: Evening -> Night (but we need to be careful here)
        // Since both old "Evening" and old "Night" become "Evening" in the new system,
        // we can't perfectly reverse this. We'll reverse Evening -> Night
        // This assumes that all "Evening" values in the down migration are from old "Night"
        DB::table('mst_members')
            ->where('timings', 'Evening')
            ->update(['timings' => 'Night']);
        
        // Same for mst_users
        DB::table('mst_users')
            ->where('timings', 'Ladies')
            ->update(['timings' => 'Evening']);
        
        DB::table('mst_users')
            ->where('timings', 'Evening')
            ->update(['timings' => 'Night']);
    }
}

