<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Restaurant;
use App\Filament\Restaurant\Resources\Orders\OrderResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class RestaurantOrdersStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $ownerId = Auth::id();
        $restaurantIds = Restaurant::where('owner_id', $ownerId)->pluck('id');

       

        $acceptedOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'accepted')
            ->count();

        $cookingOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'cooking')
            ->count();

        $readyOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'ready_to_receive')
            ->count();
 
        $deliveredOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'delivered')
            ->count();

        $totalRevenue = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'delivered')
            ->sum('total_price');

        $todayOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'pending')
            ->whereDate('created_at', Carbon::today())
            ->count();

        $weeklyRevenue = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_price');

        return [
            Stat::make('طلبات اليوم', $todayOrders)
                ->description('الطلبات الجديدة اليوم')
                ->icon('heroicon-o-calendar-days')
                ->color('primary')
                ->url(OrderResource::getUrl('index', [ 'status'=>'pending'])),

            Stat::make('مقبولة', $acceptedOrders)
                ->description('جاهزة للتحضير')
                ->icon('heroicon-o-check-circle')
                ->color('info')
                ->url(OrderResource::getUrl('index', ['status' => 'accepted'])),

            Stat::make('قيد التحضير', $cookingOrders)
                ->description('يتم تجهيزها')
                ->icon('heroicon-o-fire')
                ->color('danger')
                ->url(OrderResource::getUrl('index', ['status' => 'cooking'])),

            Stat::make('جاهز للاستلام', $readyOrders)
                ->description('مستعد للتسليم')
                ->icon('heroicon-o-truck')
                ->color('success')
                ->url(OrderResource::getUrl('index', ['status' => 'ready_to_receive'])),

            Stat::make('الطلبات المكتملة', $deliveredOrders)
                ->description('تم التسليم')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->url(OrderResource::getUrl('index', ['status' => 'delivered'])),

            Stat::make('إجمالي الإيرادات', number_format($totalRevenue, 0) . ' IQD')
                ->description('من جميع الطلبات المكتملة')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('إيرادات الأسبوع', number_format($weeklyRevenue, 0) . ' IQD')
                ->description('إيرادات هذا الأسبوع')
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
