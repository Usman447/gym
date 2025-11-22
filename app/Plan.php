<?php

namespace App;

use Lubus\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'mst_plans';

    protected $fillable = [
        'plan_code',
        'plan_name',
        'service_id',
        'plan_details',
        'days',
        'amount',
        'status',
        'created_by',
        'updated_by',
    ];

    //Eloquence Search mapping (removed - package not installed)
    use createdByUser, updatedByUser;

    protected $searchableColumns = [
        'plan_code' => 20,
        'plan_name' => 10,
        'plan_details' => 5,
    ];

    public function getPlanDisplayAttribute()
    {
        return $this->plan_name.' @ '.$this->amount.' For '.$this->days.' Days';
    }

    public function scopeExcludeArchive($query)
    {
        return $query->where('status', '!=', \constStatus::Archive);
    }

    public function scopeOnlyActive($query)
    {
        return $query->where('status', '=', \constStatus::Active);
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscription', 'plan_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    /**
     * Search scope to replace Eloquence search functionality
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
            $q->where('plan_code', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('plan_name', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('plan_details', 'LIKE', '%' . $searchTerm . '%');
        });
    }
}
