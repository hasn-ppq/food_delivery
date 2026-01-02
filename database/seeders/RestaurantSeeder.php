<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the owner user
        $owner = User::where('email', 'owner@example.com')->first();

        // Create a restaurant associated with the owner
        $restaurant = Restaurant::create([
            'owner_id' => $owner->id,
            'name' => 'Sample Restaurant',
            'description' => 'A delicious restaurant serving various meals.',
            'address' => '123 Main Street, City, Country',
            'lat' => 40.7128,
            'lng' => -74.0060,
            'status' => 'open',
            'cover_image' => 'sample_cover.jpg',
            'min_order_price' => 10.00,
            'delivery_time_estimation' => 30,
            'delivery_price_default' => 5.00,
        ]);

        // Create meals for the restaurant
        $meal1 = Meal::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Pizza Margherita',
            'description' => 'Classic pizza with tomato sauce, mozzarella, and basil.',
            'price' => 12.99,
            'image' => 'pizza_margherita.jpg',
            'status' => 'active',
            'is_featured' => true,
            'discount_price' => null,
        ]);

        $meal2 = Meal::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Burger Deluxe',
            'description' => 'Juicy burger with lettuce, tomato, and cheese.',
            'price' => 9.99,
            'image' => 'burger_deluxe.jpg',
            'status' => 'active',
            'is_featured' => false,
            'discount_price' => 8.99,
        ]);

        // Get the customer user
        $customer = User::where('email', 'customer@example.com')->first();
        $delivery = User::where('email', 'delivery@example.com')->first();

        // Create a customer order
        $order = Order::create([
            'customer_id' => $customer->id,
            'restaurant_id' => $restaurant->id,
            'delivery_id' => $delivery->id,
            'total_price' => 22.98,
            'delivery_price' => 5.00,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'pending',
            'customer_address' => '456 Customer Street, City, Country',
            'customer_lat' => 40.7128,
            'customer_lng' => -74.0060,
            'notes' => 'Extra cheese please.',
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
