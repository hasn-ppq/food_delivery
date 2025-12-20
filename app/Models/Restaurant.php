<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
   protected $fillable = [
    'owner_id',
    'name',
    'description',
    'address',
    'lat',
    'lng',
    'status',
    'cover_image',
    'min_order_price',
    'delivery_time_estimation',
    'delivery_price_default',
];

    public function owner()
{
    return $this->belongsTo(User::class, 'owner_id');
}

    public function meals()
{
    return $this->hasMany(Meal::class);
}

    public function orders()
{
    return $this->hasMany(Order::class);
}



}
