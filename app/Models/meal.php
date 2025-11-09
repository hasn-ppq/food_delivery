<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class meal extends Model
{
    protected $fillable=['name','description','price','image','restaurant_id'];
    public function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }
    
}
