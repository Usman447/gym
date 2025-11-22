<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //Eloquence Search mapping
    use createdByUser, updatedByUser;

    protected $table = 'trn_subscriptions';

    protected $fillable = [
        'member_id',
        'invoice_id',
        'plan_id',
        'status',
        'is_renewal',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at'];
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Removed $searchableColumns - was part of deprecated Sofa\Eloquence package
    // Search functionality is now handled by scopeSearch method

    public function scopeDashboardExpiring($query)
    {
        return $query
            ->whereHas('member', function ($query) {
                $query->where('status', '=', \constStatus::Active);
            })
            ->with('member')
            ->where('end_date', '<', Carbon::today()->addDays(3))
            ->where('status', '=', \constSubscription::onGoing);
    }

    public function scopeDashboardExpired($query)
    {
        return $query
            ->whereHas('member', function ($query) {
                $query->where('status', '=', \constStatus::Active);
            })
            ->with('member')
            ->where('status', '=', \constSubscription::Expired);
    }

    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeExpiring($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.end_date', '<', Carbon::today()->addDays(3))->where('trn_subscriptions.status', '=', \constSubscription::onGoing)->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.end_date', '<', Carbon::today()->addDays(3))->where('trn_subscriptions.status', '=', \constSubscription::onGoing)->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeExpired($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.status', '=', \constSubscription::Expired)->where('trn_subscriptions.status', '!=', \constSubscription::renewed)->orderBy($sorting_field, $sorting_direction);
        }

        return $query->leftJoin('mst_plans', 'trn_subscriptions.plan_id', '=', 'mst_plans.id')->select('trn_subscriptions.*', 'mst_plans.plan_name')->where('trn_subscriptions.status', '=', \constSubscription::Expired)->where('trn_subscriptions.status', '!=', \constSubscription::renewed)->whereBetween('trn_subscriptions.created_at', [$drp_start, $drp_end])->orderBy($sorting_field, $sorting_direction);
    }

    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Plan', 'plan_id');
    }

    public function invoice()
    {
        return $this->belongsTo('App\Invoice', 'invoice_id');
    }

    /**
     * Search scope to replace Eloquence search functionality
     * Note: This assumes the query already has the plan join from indexQuery
     * For member/invoice searches, the controller should add those joins
     */
    public function scopeSearch($query, $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }
        $searchTerm = trim($searchTerm, '"\'');
        if (empty($searchTerm)) {
            return $query;
        }
        return $query->where(function($q) use ($searchTerm) {
            $q->where('trn_subscriptions.start_date', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('trn_subscriptions.end_date', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('mst_plans.plan_name', 'LIKE', '%' . $searchTerm . '%');
        });
    }

    /**
     * Check and inactivate members with expired cancelled subscriptions
     * This method can be called from scheduled commands or controllers for real-time checking
     * 
     * @return int Number of members inactivated
     */
    public static function checkAndInactivateExpiredCancelled()
    {
        $today = Carbon::today();
        $inactivatedCount = 0;

        // Handle cancelled subscriptions that have passed or reached their end_date
        // Process subscriptions that ended today or earlier (same day processing for real-time)
        $cancelledSubscriptions = self::where('end_date', '<=', $today)
            ->where('status', '=', \constSubscription::cancelled)
            ->with('member')
            ->get();

        foreach ($cancelledSubscriptions as $subscription) {
            $member = $subscription->member;
            
            // Check if member exists and is currently active
            if (!$member) {
                continue;
            }
            
            // Re-fetch member to ensure we have the latest status from database
            // (Avoiding refresh() due to Eloquence compatibility)
            $member = Member::find($member->id);
            if (!$member) {
                continue;
            }
            
            // Only process if member is currently active
            if ($member->status == \constStatus::Active) {
                // Check if member has any other valid subscriptions that haven't ended yet
                // This includes:
                // 1. Ongoing subscriptions that haven't ended (end_date > today means still valid)
                // 2. Cancelled subscriptions that haven't ended yet (still in grace period)
                $hasValidSubscription = self::where('member_id', $member->id)
                    ->where('id', '!=', $subscription->id) // Exclude the current expired cancelled subscription
                    ->where(function($query) use ($today) {
                        // Check for ongoing subscriptions that are still valid
                        $query->where(function($q) use ($today) {
                            $q->where('status', '=', \constSubscription::onGoing)
                              ->where('end_date', '>', $today);
                        })
                        // OR cancelled subscriptions that are still valid (still in grace period)
                        ->orWhere(function($q) use ($today) {
                            $q->where('status', '=', \constSubscription::cancelled)
                              ->where('end_date', '>', $today);
                        });
                    })
                    ->exists();

                // If member has no valid subscriptions, inactivate them
                if (!$hasValidSubscription) {
                    $member->status = \constStatus::InActive;
                    $member->save();
                    
                    
                    $inactivatedCount++;
                }
            }
        }

        return $inactivatedCount;
    }
}
