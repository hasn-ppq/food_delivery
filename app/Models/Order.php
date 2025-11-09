<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
     use HasFactory;
    protected $fillable = ['user_id', 'restaurant_id', 'total', 'status', 'driver_id'];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
