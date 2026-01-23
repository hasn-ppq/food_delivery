<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Meal;
use App\Models\OrderItem;
use Carbon\Carbon;
use App\Events\OrderStatusChanged;

use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user(); // الكاستمر

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
                     throw new \Exception('الحد الأدنى للطلب هو ' . $restaurant->min_order_price);
                 }
            $totalPrice += $restaurant->delivery_price_default;
            // 6️⃣ تحديث السعر النهائي
            $order->update([
                 'total_price' => $totalPrice,
                 'delivery_price' => $restaurant->delivery_price_default,
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
    $user = Auth::user();

    $orders = Order::with(['restaurant:id,name', 'items'])
        ->where('customer_id', $user->id)
        ->whereNotIn('status', ['delivered', 'canceled'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'orders' => $orders
    ]);
}
public function show(Order $order)
{
    $user = auth::user();

    // تأكد انه الطلب يخص هذا الكاستمر
    if ($order->customer_id !== $user->id) {
        return response()->json([
            'message' => 'غير مصرح'
        ], 403);
    }

    // تحميل العلاقات
    $order->load([
        'restaurant:id,name,address,lat,lng',
        'items'
    ]);

    return response()->json([
        'order' => $order
    ]);
}

  public function cancel(Order $order)
{
    $user = auth::user();

    // تأكدالطلب إله
    if ( $order->customer_id !== $user->id) {
        return response()->json([
            'message' => 'غير مصرح'
        ], 403);
    }

    // تحقق من حالة الطلب
    if (!in_array($order->status, ['pending', 'accepted'])) {
        return response()->json([
            'message' => 'لا يمكن إلغاء الطلب في هذه المرحلة'
        ], 422);
    }

    // إلغاء الطلب
    $oldStatus = $order->status;
    $order->update([
        'status' => 'canceled',
        'canceled_reason' => 'Canceled by customer',
    ]);

    // إرسال حدث تغيير حالة الطلب
    event(new OrderStatusChanged($order, $oldStatus, 'canceled'));

    return response()->json([
        'message' => 'تم إلغاء الطلب بنجاح'
    ]);
}
public function myOrdersHistory()
{
    $user = auth::user();

    $orders = Order::with(['restaurant:id,name', 'items'])
        ->where('customer_id', $user->id)
        ->whereIn('status', ['delivered', 'canceled'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'orders' => $orders
    ]);
}

}
