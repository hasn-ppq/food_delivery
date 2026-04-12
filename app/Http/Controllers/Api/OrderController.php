<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        $missingProfileFields = [];
        if (!$user->name || trim((string) $user->name) === '') {
            $missingProfileFields[] = 'name';
        }

        if (!$user->phone || trim((string) $user->phone) === '') {
            $missingProfileFields[] = 'phone';
        }

        if (is_null($user->lat)) {
            $missingProfileFields[] = 'lat';
        }

        if (is_null($user->lng)) {
            $missingProfileFields[] = 'lng';
        }

        if (!empty($missingProfileFields)) {
            return response()->json([
                'message' => 'Please complete your profile information before placing an order.',
                'profile_incomplete' => true,
                'missing_fields' => $missingProfileFields,
            ], 422);
        }

        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array|min:1',
            'items.*.meal_id' => 'required|exists:meals,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer_lat' => 'required|numeric',
            'customer_lng' => 'required|numeric',
            'customer_address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $restaurant = Restaurant::where('id', $request->restaurant_id)
            ->where('status', 'open')
            ->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant is not available now.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $order = Order::create([
                'customer_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_price' => 0,
                'delivery_price' => 0,
                'customer_lat' => $request->customer_lat,
                'customer_lng' => $request->customer_lng,
                'customer_address' => $request->customer_address,
                'notes' => $request->notes,
            ]);

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $meal = Meal::where('id', $item['meal_id'])
                    ->where('restaurant_id', $restaurant->id)
                    ->where('status', 'active')
                    ->first();

                if (!$meal) {
                    throw new \Exception('Invalid meal item.');
                }

                $price = $meal->discount_price ?? $meal->price;
                $itemTotal = $price * $item['quantity'];
                $totalPrice += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                    'meal_name' => $meal->name,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $itemTotal,
                ]);
            }

            if ($totalPrice < $restaurant->min_order_price) {
                throw new \Exception('Minimum order is ' . $restaurant->min_order_price);
            }

            $totalPrice += $restaurant->delivery_price_default;

            $order->update([
                'total_price' => $totalPrice,
                'delivery_price' => $restaurant->delivery_price_default,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order created successfully.',
                'order_id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function myOrders()
    {
        $user = Auth::user();

        $orders = Order::with(['restaurant:id,name', 'items'])
            ->where('customer_id', $user->id)
            ->whereNotIn('status', ['delivered', 'canceled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        if ($order->customer_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->load([
            'restaurant:id,name,address,lat,lng',
            'items',
        ]);

        return response()->json([
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        $user = Auth::user();

        if ($order->customer_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'accepted'])) {
            return response()->json([
                'message' => 'Cannot cancel order at this stage.',
            ], 422);
        }

        $oldStatus = $order->status;
        $order->update([
            'status' => 'canceled',
            'canceled_reason' => 'Canceled by customer',
        ]);

        event(new OrderStatusChanged($order, $oldStatus, 'canceled'));

        return response()->json([
            'message' => 'Order canceled successfully.',
        ]);
    }

    public function myOrdersHistory()
    {
        $user = Auth::user();

        $orders = Order::with(['restaurant:id,name', 'items'])
            ->where('customer_id', $user->id)
            ->whereIn('status', ['delivered', 'canceled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
