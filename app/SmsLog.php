<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = 'trn_sms_log';

    protected $fillable = [
            'shoot_id',
            'number',
            'message',
            'status',
            'sender_id',
            'send_time',
    ];

    public $timestamps = false;

    protected $dates = ['send_time'];

    //Eloquence Search mapping

    protected $searchableColumns = [
        'number' => 20,
        'message' => 10,
        'status' => 5,
    ];

    public function scopeDashboardLogs($query)
    {
        return $query->where('send_time', '<=', Carbon::now())->take(5)->orderBy('send_time', 'desc');
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
            $q->where('number', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('message', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('status', 'LIKE', '%' . $searchTerm . '%');
        });
    }
}
