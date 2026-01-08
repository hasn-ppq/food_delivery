<?php

namespace App\Filament\Driver\Resources\MyOrders\Pages;

use App\Filament\Driver\Resources\MyOrders\MyOrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Models\Order;

class EditMyOrder extends EditRecord
{
    protected static string $resource = MyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
           
          Action::make('back')
            ->label('رجوع')
            ->url($this->getResource()::getUrl('index')),
        ];
    }
    protected function getFormActions(): array
    {
        return [
             Action::make('track')
            ->label('تتبع موقع الزبون')
            ->icon('heroicon-o-map')
            ->color('info')
            ->url(fn (Order $record) =>
                 "https://www.google.com/maps/dir/?api=1&destination={$record->customer_lat},{$record->customer_lng}"
            )
            ->openUrlInNewTab()
            ->visible(fn (Order $record) =>
                $record->customer_lat !== null &&
                $record->customer_lng !== null &&
                in_array($record->status, ['on_the_way'])
            ),
           
          
        ];
    }

   
}
