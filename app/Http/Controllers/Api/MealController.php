<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;


class MealController extends Controller
{
      public function index(Restaurant $restaurant)
    {
        // تأكد المطعم مفتوح
        if ($restaurant->status !== 'open') {
            return response()->json([
                'message' => 'المطعم مغلق حالياً'
            ], 403);
        }

        $meals = $restaurant->meals()
            ->where('status', 'active')
            ->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json($meals);
    }
}
