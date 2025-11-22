<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    protected $table = 'trn_food_orders';

    protected $fillable = [
        'order_number',
        'total_amount',
        'payment_mode',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at'];

    //Eloquence Search mapping
    use createdByUser, updatedByUser;

    protected $searchableColumns = [
        'order_number' => 20,
        'total_amount' => 10,
    ];

    public function orderItems()
    {
        return $this->hasMany('App\FoodOrderItem', 'food_order_id');
    }
}
