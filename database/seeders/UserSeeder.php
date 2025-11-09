<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);

    User::create([
        'name' => 'Restaurant Owner',
        'email' => 'owner@example.com',
        'password' => Hash::make('password'),
        'role' => 'restaurant_owner',
    ]);

    User::create([
        'name' => 'Customer User',
        'email' => 'customer@example.com',
        'password' => Hash::make('password'),
        'role' => 'customer',
    ]);

    User::create([
        'name' => 'Driver User',
        'email' => 'driver@example.com',
        'password' => Hash::make('password'),
        'role' => 'driver',
    ]);
     $admin = User::where('email', 'admin@example.com')->first();
     $admin->assignRole('admin');

     $owner = User::where('email', 'owner@example.com')->first();
     $owner->assignRole('restaurant_owner');

     $customer = User::where('email', 'customer@example.com')->first();
     $customer->assignRole('customer');

     $driver = User::where('email', 'driver@example.com')->first();
     $driver->assignRole('driver');

    
    }
}
