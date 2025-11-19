<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    //Eloquence Search mapping
    use Eloquence;
    use createdByUser, updatedByUser;

    protected $table = 'mst_enquiries';

    protected $fillable = [
        'name',
        'DOB',
        'age',
        'email',
        'address',
        'opf_residence',
        'status',
        'gender',
        'contact',
        'pin_code',
        'occupation',
        'start_by',
        'interested_in',
        'aim',
        'source',
        'created_by',
        'created_by_user_name',
        'created_by_user_email',
        'updated_by',
        'updated_by_user_name',
        'updated_by_user_email',
    ];

    protected $searchableColumns = [
        'name' => 20,
        'email' => 20,
        'contact' => 20,
    ];

    public function Followups()
    {
        return $this->hasMany('App\Followup');
    }

    public function scopeIndexQuery($query, $sorting_field, $sorting_direction, $drp_start, $drp_end)
    {
        $sorting_field = ($sorting_field != null ? $sorting_field : 'created_at');
        $sorting_direction = ($sorting_direction != null ? $sorting_direction : 'desc');

        if ($drp_start == null or $drp_end == null) {
            return $query->select('mst_enquiries.id', 'mst_enquiries.name', 'mst_enquiries.contact', 'mst_enquiries.email', 'mst_enquiries.address', 'mst_enquiries.gender', 'mst_enquiries.created_at', 'mst_enquiries.status', 'mst_enquiries.created_by')->orderBy($sorting_field, $sorting_direction);
        }

        return $query->select('mst_enquiries.id', 'mst_enquiries.name', 'mst_enquiries.contact', 'mst_enquiries.email', 'mst_enquiries.address', 'mst_enquiries.gender', 'mst_enquiries.created_at', 'mst_enquiries.status', 'mst_enquiries.created_by')->whereBetween('mst_enquiries.created_at', [
            $drp_start,
            $drp_end,
        ])->orderBy($sorting_field, $sorting_direction);
    }

    public function scopeOnlyLeads($query)
    {
        return $query->where('status', '=', \constEnquiryStatus::Lead)->orderBy('created_at', 'desc')->take(10);
    }
}
