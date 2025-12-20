<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;






class RestaurantController extends Controller
{
     public function index(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = $request->lat;
        $lng = $request->lng;

        // Haversine Formula
        $restaurants = Restaurant::select(
                'restaurants.*',
                DB::raw("
                    (6371 * acos(
                        cos(radians($lat)) *
                        cos(radians(lat)) *
                        cos(radians(lng) - radians($lng)) +
                        sin(radians($lat)) *
                        sin(radians(lat))
                    )) AS distance
                ")
            )
            ->where('status', 'open')
            ->orderBy('distance')
            ->get();

        return response()->json($restaurants);
    }
}
