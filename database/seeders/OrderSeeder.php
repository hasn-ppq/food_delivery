<?php

namespace Database\Seeders;
use App\Models\meal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     $customer = User::where('role', 'customer')->first();
    $restaurant = Restaurant::first();
    $meal = Meal::first();

    $order = Order::create([
        'user_id' => $customer->id,
        'restaurant_id' => $restaurant->id,
        'total' => 15.50,
        'status' => 'pending',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'meal_id' => $meal->id,
        'quantity' => 2,
        'price' => 7.75,
    ]);

    }
    
}
