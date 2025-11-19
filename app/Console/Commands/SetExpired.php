<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Subscription;
use App\Member;
use Illuminate\Console\Command;

class SetExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and sets expired subscription and inactivates members with expired cancelled subscriptions';

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
        $today = Carbon::today();

        // Set ongoing subscriptions that have passed end_date to expired
        Subscription::where('end_date', '<', $today)
            ->where('status', '=', \constSubscription::onGoing)
            ->update(['status' => \constSubscription::Expired]);

        // Check and inactivate members with expired cancelled subscriptions
        // This uses <= to process subscriptions ending today for real-time behavior
        $inactivatedCount = Subscription::checkAndInactivateExpiredCancelled();
        
        if ($inactivatedCount > 0) {
            $this->info("Inactivated {$inactivatedCount} member(s) with expired cancelled subscriptions.");
        }
    }
}
