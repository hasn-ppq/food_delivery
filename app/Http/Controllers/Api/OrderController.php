<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Meal;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user(); // الكاستمر

        // 1️⃣ تحقق أساسي
        if ($user->role->slug !== 'customer') {
            return response()->json([
                'message' => 'غير مصرح'
            ], 403);
        }

        // 2️⃣ Validation
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

        // 3️⃣ تحقق المطعم
        $restaurant = Restaurant::where('id', $request->restaurant_id)
            ->where('status', 'open')
            ->first();

        if (!$restaurant) {
            return response()->json([
                'message' => 'المطعم غير متاح حالياً'
            ], 422);
        }

        DB::beginTransaction();

        try {

            // 4️⃣ إنشاء الطلب
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

            // 5️⃣ إضافة الوجبات
            foreach ($request->items as $item) {

                $meal = Meal::where('id', $item['meal_id'])
                    ->where('restaurant_id', $restaurant->id)
                    ->where('status', 'active')
                    ->first();

                if (!$meal) {
                    throw new \Exception('وجبة غير صالحة');
                }

                $itemTotal = $meal->price * $item['quantity'];
                $totalPrice += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                    'meal_name' => $meal->name,
                    'quantity' => $item['quantity'],
                    'price' => $meal->price,
                    'total' => $itemTotal,
                ]);
            }

            // 6️⃣ تحديث السعر النهائي
            $order->update([
                'total_price' => $totalPrice
            ]);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح',
                'order_id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'فشل إنشاء الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function myOrders()
{
    $user = auth::user();

    // تأكد انه كاستمر
    if ($user->role->slug !== 'customer') {
        return response()->json([
            'message' => 'غير مصرح'
        ], 403);
    }

    $orders = Order::with(['restaurant:id,name', 'items'])
        ->where('customer_id', $user->id)
        ->whereNotIn('status', ['delivered', 'canceled'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'orders' => $orders
    ]);
}

}
