<?php

namespace App\Filament\Driver\Widgets;

use App\Filament\Driver\Resources\MyOrders\MyOrderResource;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Filament\Driver\Resources\Orders\OrderResource as OrdersResource;


class DriverOrdersStats extends StatsOverviewWidget
{
   
    protected function getStats(): array
    {
        $driverId = Auth::id();
        $driverLat = Auth::user()->lat;
        $driverLng = Auth::user()->lng;

        return [

            //  الطلبات المتاحة
            Stat::make(
                'الطلبات المتاحة',
                Order::select('*')
        ->selectRaw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(customer_lat)) *
                cos(radians(customer_lng) - radians(?)) +
                sin(radians(?)) *
                sin(radians(customer_lat))
            )) AS distance
        ", [$driverLat, $driverLng, $driverLat])
        ->having('distance', '<=', 3)
        ->whereNull('delivery_id')
        ->where('status', 'ready_to_receive')
                    ->count()
            )
            ->description('بانتظار موافقتك')
            ->icon('heroicon-o-bolt')
            ->color('warning')->url(OrdersResource::getUrl()),

            //  الطلبات المستلمة
            Stat::make(
                'طلباتي الحالية',
                Order::where('delivery_id', $driverId)
                    ->whereIn('status', [ 'on_the_way'])
                    ->count()
            )
            ->description('قيد التوصيل')
            ->icon('heroicon-o-truck')
            ->color('info')->url(
    MyOrderResource::getUrl('index', [
        'status' => 'on_the_way', // 🔹 parameter مباشر
    ])
    ),
           
            // الطلبات المكتملة
            Stat::make(
                'طلبات مكتملة',
                Order::where('delivery_id', $driverId)
                    ->where('status', 'delivered')
                    ->count()
            )
            ->description('تم التوصيل')
            ->icon('heroicon-o-check-circle')
            ->color('success')->url(
    MyOrderResource::getUrl('index', [
        'status' => 'delivered', 
    ])
    ),
        ];
    }
}
