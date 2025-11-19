<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'mst_inventory';

    protected $fillable = [
        'name',
        'amount',
        'quantity',
        'created_by',
        'updated_by',
    ];

    //Eloquence Search mapping
    use Eloquence;
    use createdByUser, updatedByUser;

    protected $searchableColumns = [
        'name' => 20,
    ];
}
