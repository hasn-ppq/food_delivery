<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
     use HasFactory;
   protected $fillable = [
    'customer_id',
    'restaurant_id',
    'delivery_id',
    'total_price',
    'delivery_price',
    'payment_method',
    'payment_status',
    'status',
    'customer_address',
    'customer_lat',
    'customer_lng',
    'notes',
    'delivery_assigned_at',
    'delivered_at',
    'canceled_reason',
];


   public function customer()
{
    return $this->belongsTo(User::class, 'customer_id');
}

   public function restaurant()
{
    return $this->belongsTo(Restaurant::class);
}

   public function delivery()
{
    return $this->belongsTo(User::class, 'delivery_id');
}

   public function items()
{
    return $this->hasMany(OrderItem::class);
}

}
