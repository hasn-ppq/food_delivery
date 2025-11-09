<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\Meal;
use App\Models\User;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
     $owner = User::where('role', 'restaurant_owner')->first();

    $restaurant = Restaurant::create([
        'name' => 'مطعم الريان',
        'address' => 'بغداد - الكرادة',
        'phone' => '07701234567',
        'user_id' => $owner->id,
    ]);

    Meal::create([
        'name' => 'برياني دجاج',
        'description' => 'برياني عراقي حار',
        'price' => 5.00,
        'restaurant_id' => $restaurant->id,
    ]);

    Meal::create([
        'name' => 'شاورما لحم',
        'description' => 'شاورما مع خبز عراقي',
        'price' => 3.50,
        'restaurant_id' => $restaurant->id,
    ]);

}
}
