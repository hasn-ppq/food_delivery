<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'otp_sent_at',
        'otp_sms_available_at',
        'otp_channel',
        'last_login_at',
        'lat',
        'lng',
        'is_online',
        'is_active',
        'device_token',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
            'otp_sent_at' => 'datetime',
            'otp_sms_available_at' => 'datetime',
            'last_login_at' => 'datetime',
            'otp_attempts' => 'integer',
            'is_online' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function restaurants()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    public function ordersAsCustomer()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ordersAsDelivery()
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }
}
