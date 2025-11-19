<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Attendance;
use Illuminate\Console\Command;

class CleanOldAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete attendance records older than 30 days';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Calculate date 31 days ago (records from 31st day and older should be deleted)
        $thirtyOneDaysAgo = Carbon::today()->subDays(31);
        
        // Delete all records from 31st day and older
        $deletedCount = Attendance::where('check_in_time', '<', $thirtyOneDaysAgo->startOfDay())
            ->delete();
        
        if ($deletedCount > 0) {
            $this->info("Deleted {$deletedCount} attendance record(s) older than 30 days.");
        } else {
            $this->info("No old attendance records to delete.");
        }
        
        return 0;
    }
}

