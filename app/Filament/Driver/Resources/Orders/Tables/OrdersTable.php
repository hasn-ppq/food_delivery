<?php

namespace App\Filament\Driver\Resources\Orders\Tables;

use App\Filament\Admin\Meals\Pages\EditMeal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Order;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Events\OrderStatusChanged;


class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
         ->poll('5s')
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('restaurant.name')->label('المطعم'),
                TextColumn::make('customer.name')->label('الزبون'),
                TextColumn::make('total_price')
                ->label(' السعر الكلي ')
                ->money('IQD'),
                TextColumn::make('customer_address')->label('عنوان الزبون'),
                TextColumn::make('delivery_price')->label('سعر التوصيل'),
                TextColumn::make('created_at')->since(),
        ])
            ->filters([
                //
            ])
            ->recordActions([
               EditAction::make('تفاصيل الطلب')
               ->label('تفاصيل الطلب'),

               Action::make('accept')
    ->label('استلام الطلب')
    ->color('success')
    ->requiresConfirmation()
    ->action(function (Order $record) {

        $driverId = Auth::id();

        // عدد الطلبات الحالية للدرايفر
        $activeOrdersCount = Order::where('delivery_id', $driverId)
            ->whereIn('status', ['on_the_way']) //
            ->count();

        if ($activeOrdersCount >= 2) {
            Notification::make()
                ->title('لا يمكن استلام الطلب')
                ->body('. يرجى إكمال الطلبات الحالية قبل استلام طلب جديد')
                ->danger()
                ->send();

            return;
        }
          $oldStatus = $record->status;
        // قبول الطلب
        $record->update([
            'delivery_id' => $driverId,
            'previous_status' => $oldStatus,
            'status' => 'on_the_way',
            'delivery_assigned_at' => now(),
        ]);

        Notification::make()
            ->title('تم استلام الطلب بنجاح')
            ->success()
            ->send();
              event(new OrderStatusChanged(
            $record,
            $oldStatus,
            'on_the_way'
        ));
    })
     ->visible(fn (Order $record) =>
        $record->status === 'ready_to_receive' &&
        $record->delivery_id === null
       ),
              
            
            ]);
    }
}
