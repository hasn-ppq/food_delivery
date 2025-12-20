<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class meal extends Model
{
   protected $fillable = [
    'restaurant_id',
    'name',
    'description',
    'price',
    'image',
    'status',
    'is_featured',
    'discount_price',
];



    public function restaurant()
{
    return $this->belongsTo(Restaurant::class);
}

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

}
