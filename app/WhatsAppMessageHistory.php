<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessageHistory extends Model
{

    protected $table = 'trn_whatsapp_message_history';

    protected $fillable = [
        'member_id',
        'subscription_id',
        'reminder_number',
        'subscription_end_date',
        'message',
        'phone_number',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $dates = ['created_at', 'updated_at', 'sent_at', 'subscription_end_date'];

    protected $searchableColumns = [
        'Member.name' => 20,
        'Member.member_code' => 20,
        'phone_number' => 10,
        'message' => 5,
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo('App\Member', 'member_id');
    }

    public function subscription()
    {
        return $this->belongsTo('App\Subscription', 'subscription_id');
    }

    // Scopes
    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'sent_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        $query = $query->leftJoin('mst_members', 'trn_whatsapp_message_history.member_id', '=', 'mst_members.id')
            ->select('trn_whatsapp_message_history.*', 'mst_members.name as member_name', 'mst_members.member_code');

        // Handle sorting field - qualify with table name if needed
        if ($sorting_field == 'member_name') {
            $query->orderBy('mst_members.name', $sorting_direction);
        } elseif ($sorting_field == 'sent_at') {
            $query->orderBy('trn_whatsapp_message_history.sent_at', $sorting_direction);
        } elseif ($sorting_field == 'reminder_number') {
            $query->orderBy('trn_whatsapp_message_history.reminder_number', $sorting_direction);
        } else {
            // Default to sent_at if field not recognized
            $query->orderBy('trn_whatsapp_message_history.sent_at', $sorting_direction);
        }

        if ($drp_start != null && $drp_end != null) {
            $query->whereBetween('trn_whatsapp_message_history.sent_at', [$drp_start, $drp_end]);
        }

        return $query;
    }

    // Helper methods
    public function getReminderTypeAttribute()
    {
        switch ($this->reminder_number) {
            case 1:
                return 'First Reminder';
            case 2:
                return 'Second Reminder';
            case 3:
                return 'Third Reminder';
            default:
                return 'Unknown';
        }
    }
}

