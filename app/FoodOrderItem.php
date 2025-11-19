<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoodOrderItem extends Model
{
    protected $table = 'trn_food_order_items';

    protected $fillable = [
        'food_order_id',
        'food_item_id',
        'inventory_id',
        'quantity',
        'item_amount',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['created_at', 'updated_at'];

    use createdByUser, updatedByUser;

    public function foodOrder()
    {
        return $this->belongsTo('App\FoodOrder', 'food_order_id');
    }

    public function foodItem()
    {
        return $this->belongsTo('App\FoodItem', 'food_item_id');
    }

    public function inventory()
    {
        return $this->belongsTo('App\Inventory', 'inventory_id');
    }
}
