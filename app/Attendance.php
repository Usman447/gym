<?php

namespace App;

use Carbon\Carbon;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use Eloquence;
    use createdByUser, updatedByUser;

    protected $table = 'trn_attendance';

    protected $fillable = [
        'member_id',
        'check_in_time',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['check_in_time', 'created_at', 'updated_at'];

    protected $searchableColumns = [
        'Member.name' => 20,
        'Member.member_code' => 20,
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    /**
     * Scope to get attendance for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereRaw('DATE(check_in_time) = ?', [$date]);
    }

    /**
     * Scope to get attendance for date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
    }

    /**
     * Scope to get today's attendance
     */
    public function scopeToday($query)
    {
        $today = Carbon::today()->format('Y-m-d');
        return $query->whereRaw('DATE(check_in_time) = ?', [$today]);
    }

    /**
     * Scope to get attendance for last 7 days
     */
    public function scopeLast7Days($query)
    {
        $sevenDaysAgo = Carbon::today()->subDays(7);
        return $query->where('check_in_time', '>=', $sevenDaysAgo->startOfDay());
    }

    /**
     * Get total visits count for a member on a specific date
     */
    public static function getTodayVisitsCount($memberId, $date = null)
    {
        if ($date === null) {
            $date = Carbon::today()->format('Y-m-d');
        } elseif ($date instanceof Carbon) {
            $date = $date->format('Y-m-d');
        }
        
        return self::where('member_id', $memberId)
            ->whereRaw('DATE(check_in_time) = ?', [$date])
            ->count();
    }
}
