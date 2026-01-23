<?php

namespace App\Filament\Driver\Resources\MyOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use App\Filament\Driver\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Events\OrderStatusChanged;



class MyOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 
             TextColumn::make('id')
                ->label('رقم الطلب')
                ->sortable(),

             TextColumn::make('status')
                ->label('الحالة')
                ->badge()
                ->colors([
                    'warning' => 'on_the_way',
                    'success' => 'delivered',
                    'danger'  => 'canceled',
                ]),

             TextColumn::make('customer_address')
                ->label('عنوان الزبون')
                ->wrap(),
             TextColumn::make('restaurant.address')
                ->label('عنوان المطعم')
                ->wrap(),

             TextColumn::make('total_price')
                ->label('المبلغ')
                ->money('IQD'),
             TextColumn::make('delivery_price')->label('سعر التوصيل')
        
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('حالة الطلب')
                ->options([
                    'on_the_way' => 'بالطريق',
                    'delivered' => 'تم التوصيل',
                ]),
        ])
            ->recordActions([
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

         EditAction::make('تفاصيل الطلب')
                ->label('تفاصيل الطلب'),
        Action::make('delivered')
            ->label('تم التوصيل')
            ->color('success')
            ->requiresConfirmation()
           ->action(function (Order $record) {

        $oldStatus = $record->status;

        $record->update([
            'previous_status' => $oldStatus,
            'status' => 'delivered',
        ]);

        event(new OrderStatusChanged(
            $record,
            $oldStatus,
            'delivered'
        ));
    })
            ->visible(fn (Order $record) => $record->status === 'on_the_way'),
    
            ])
           ;
    }
}
