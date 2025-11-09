<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء صلاحيات
    Permission::create(['name' => 'manage restaurants']);
    Permission::create(['name' => 'manage meals']);
    Permission::create(['name' => 'manage orders']);
    Permission::create(['name' => 'deliver orders']);

    // إنشاء أدوار وربط الصلاحيات
    $admin = Role::create(['name' => 'admin']);
    $admin->givePermissionTo(Permission::all());

    $owner = Role::create(['name' => 'restaurant_owner']);
    $owner->givePermissionTo(['manage restaurants', 'manage meals', 'manage orders']);

    $customer = Role::create(['name' => 'customer']);
    // ما يحتاج صلاحيات خاصة (فقط يطلب وجبات)

    $driver = Role::create(['name' => 'driver']);
    $driver->givePermissionTo(['deliver orders']);

    }
}
