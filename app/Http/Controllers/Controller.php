<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;


abstract class Controller
{
    public function cancel(Request $request)
    {
        $request->validate([
            'restaurant_id'=>'required',
            'order_id'=>'required',
           
        ]);
        $user=Auth::user();
        if($user->role->slug!=='customer'){
            return response()->json([
                'message'=>'لا يجوز الدخول'
            ],403);
        }

        $order=Order::where('id',$request->order_id);

        if(!$order){
            return response()->json(['message'=>'الطلب غير موجود'],404);
        }
         $order->update([
                'status'=>'canceled'
            ]);
        return response()->json(['massage'=>'تم الغاء الطلب']);
    }
}
