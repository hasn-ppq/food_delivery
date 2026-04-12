<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        User::create([
            'name' => 'Admin User',
            'phone' => '07700000000',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        $ownerRole = Role::where('slug', 'owner')->first();
        User::create([
            'name' => 'Restaurant Owner',
            'phone' => '07733333333',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role_id' => $ownerRole->id,
        ]);
    }
}
