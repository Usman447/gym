<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $table = 'mst_food_items';

    protected $fillable = [
        'name',
        'amount',
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
