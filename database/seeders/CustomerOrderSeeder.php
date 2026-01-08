<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Meal;
use Illuminate\Support\Facades\Hash;

class CustomerOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the customer role
        $customerRole = Role::where('slug', 'customer')->first();

        // Create a new customer user
        $customer = User::create([
            'name' => 'Customer Two',
            'phone' => '07733333333',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
        ]);

        // Get the delivery user (assuming it exists from CustomerDriverSeeder)
        $delivery = User::where('email', 'delivery@example.com')->first();

        // Get the restaurant (assuming it exists from RestaurantSeeder)
        $restaurant = Restaurant::first();

        // Get meals from the restaurant
        $meals = Meal::where('restaurant_id', $restaurant->id)->get();

        // Calculate total price (example: order two meals)
        $meal1 = $meals->first();
        $meal2 = $meals->skip(1)->first();
        $totalPrice = $meal1->price + $meal2->price;
        $deliveryPrice = $restaurant->delivery_price_default;

        // Create an order
        $order = Order::create([
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'delivery_id' => $delivery->id,
            'total_price' => $totalPrice,
            'delivery_price' => $deliveryPrice,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'status' => 'pending',
            'customer_address' => '789 Customer Avenue, City, Country',
            'customer_lat' => 40.7128,
            'customer_lng' => -74.0060,
            'notes' => 'Extra spicy please.',
        ]);

        // Create order items
        OrderItem::create([
            'order_id' => $order->id,
            'meal_id' => $meal1->id,
            'meal_name' => $meal1->name,
            'quantity' => 1,
            'price' => $meal1->price,
            'total' => $meal1->price,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'meal_id' => $meal2->id,
            'meal_name' => $meal2->name,
            'quantity' => 1,
            'price' => $meal2->price,
            'total' => $meal2->price,
        ]);
    }
}
