<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsTrigger extends Model
{
    //Eloquence Search mapping
    use createdByUser, updatedByUser;

    const CREATED_AT = null;

    protected $table = 'mst_sms_triggers';

    protected $fillable = [
        'name',
        'alias',
        'message',
        'status',
        'updated_by',
    ];

    protected $searchableColumns = [
        'name' => 20,
        'message' => 10,
    ];
}
