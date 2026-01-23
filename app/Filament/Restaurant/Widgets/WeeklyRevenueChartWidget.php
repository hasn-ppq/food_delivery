<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class WeeklyRevenueChartWidget extends ChartWidget
{
    protected  ?string $heading = 'إيرادات الأسبوع';

    protected function getData(): array
    {
        $ownerId = Auth::id();
        $restaurantIds = Restaurant::where('owner_id', $ownerId)->pluck('id');

        // Get revenue data for the current week (7 days)
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Order::whereIn('restaurant_id', $restaurantIds)
                ->where('status', 'delivered')
                ->whereDate('created_at', $date)
                ->sum('total_price');

            $data[] = $revenue;
            $labels[] = $date->format('D');
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات اليومية',
                    'data' => $data,
                    'borderColor' => 'rgb(153, 102, 255)',
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
