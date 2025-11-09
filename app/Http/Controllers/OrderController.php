<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // ✅ Customer: إنشاء طلب جديد
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array',
            'items.*.meal_id' => 'required|exists:meals,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $meal = Meal::findOrFail($item['meal_id']);
            $price = $meal->price * $item['quantity'];

            $total += $price;

            $orderItems[] = [
                'meal_id' => $meal->id,
                'quantity' => $item['quantity'],
                'price' => $meal->price,
            ];
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $request->restaurant_id,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach ($orderItems as $item) {
            $item['order_id'] = $order->id;
            OrderItem::create($item);
        }

        return response()->json(['message' => 'Order created successfully', 'order' => $order->load('items')], 201);
    }

    // ✅ Owner: عرض الطلبات الخاصة بمطعمه
    public function restaurantOrders()
    {
        $user = Auth::user();

        if ($user->role !== 'restaurant_owner') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['customer', 'items.meal'])
            ->where('restaurant_id', $user->id) // نفترض owner هو صاحب المطعم
            ->get();

        return response()->json($orders);
    }

    // ✅ Driver: عرض الطلبات المسندة له
    public function driverOrders()
    {
        $user = Auth::user();

        if ($user->role !== 'driver') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['restaurant', 'customer'])
            ->where('driver_id', $user->id)
            ->get();

        return response()->json($orders);
    }

    // ✅ Driver: تحديث حالة التوصيل
    public function updateStatus(Request $request, Order $order)
    {
        $user = Auth::user();

        if ($user->role !== 'driver' || $order->driver_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,on_the_way,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }

    // ✅ Admin: عرض كل الطلبات
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = Order::with(['customer', 'restaurant', 'driver', 'items.meal'])->get();

        return response()->json($orders);
    }
}
