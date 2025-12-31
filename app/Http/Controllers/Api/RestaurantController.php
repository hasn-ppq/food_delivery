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
        $radius = $request->radius ?? 10;
        $restaurants = Restaurant::select(
                 'id',
                 'name',
                 'address',
                 'lat',
                 'lng',
                 'cover_image',
                'delivery_price_default',
                'min_order_price',
                DB::raw("
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(lat)) *
                        cos(radians(lng) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(lat))
                    )) AS distance
                ")
            )
            ->setBindings([$lat, $lng, $lat])
            ->where('status', 'open')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->paginate(10);

        return response()->json([
            'restaurants' => $restaurants
        ]);
    }
}
