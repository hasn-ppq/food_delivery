<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class TotalRevenueChartWidget extends ChartWidget
{
    protected  ?string $heading = 'إجمالي الإيرادات';

    protected function getData(): array
    {
        $ownerId = Auth::id();
        $restaurantIds = Restaurant::where('owner_id', $ownerId)->pluck('id');

        // Get revenue data for the last 30 days
        $data = [];
        $labels = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Order::whereIn('restaurant_id', $restaurantIds)
                ->where('status', 'delivered')
                ->whereDate('created_at', $date)
                ->sum('total_price');

            $data[] = $revenue;
            $labels[] = $date->format('M d');
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات اليومية',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
