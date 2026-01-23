<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use App\Filament\Admin\Orders\OrderResource;
use App\Filament\Admin\Users\UserResource;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use Filament\Actions\Action;
class AdminOrdersStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalInactiveDriversAndOwners = User::where('is_active', false)
    ->whereHas('role', function ($q) {
        $q->whereIn('slug', ['delivery', 'owner']);
    })
    ->count();

        $totalOrders = Order::count();
        $totalRestaurants = Restaurant::count();
       $totalActiveDrivers = User::where('is_active', true)
    ->whereHas('role', function ($q) {
        $q->where('name', 'delivery');
    }) ->count();

        $totalUsers = User::count();
        
        $deliveredOrders = Order::where('status', 'delivered')->count();

        return [
             Stat::make('غير المفعّلين (سائق + مطعم)', $totalInactiveDriversAndOwners)
               ->description('بحاجة إلى موافقة')
               ->icon('heroicon-o-exclamation-triangle')
               ->color('warning')
               ->url(UserResource::getUrl()),
                
            Stat::make('إجمالي الطلبات', $totalOrders)
                ->description('عدد جميع الطلبات')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),
                

            Stat::make('إجمالي المطاعم', $totalRestaurants)
                ->description('عدد المطاعم المسجلة')
                ->icon('heroicon-o-building-storefront')
                ->color('info'),
                
             Stat::make('إجمالي السائقين', $totalActiveDrivers)
               ->description('السائقين المفعّلين')
               ->icon('heroicon-o-truck')
               ->color('info'),

            Stat::make('إجمالي المستخدمين', $totalUsers)
                ->description('عدد جميع المستخدمين')
                ->icon('heroicon-o-users')
                ->color('warning'),

           
            Stat::make('الطلبات المكتملة', $deliveredOrders)
                ->description('الطلبات المسلمة')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
                
        ];
    }
}

