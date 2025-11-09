<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'user_id'];

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'user-id');
    }
    public function orders()
{
    return $this->hasMany(Order::class);
}


}
