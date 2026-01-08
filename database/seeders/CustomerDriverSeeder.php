<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class CustomerDriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $deliveryRole = Role::where('slug', 'delivery')->first();

        User::create([
            'name' => 'Customer User',
            'phone' => '07711111111',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
        ]);

        User::create([
            'name' => 'Delivery User',
            'phone' => '07722222222',
            'email' => 'delivery@example.com',
            'password' => Hash::make('password'),
            'role_id' => $deliveryRole->id,
        ]);
    }
}
