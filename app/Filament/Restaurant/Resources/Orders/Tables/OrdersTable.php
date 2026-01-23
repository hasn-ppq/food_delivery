<?php

namespace App\Filament\Restaurant\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
 use Filament\Tables\Filters\SelectFilter;
use App\Events\OrderStatusChanged;
use App\Models\Order;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->columns([
                 TextColumn::make('id')->label('رقم الطلب'),
                TextColumn::make('customer.name')->label('الزبون'),
                TextColumn::make('total_price')->label('المجموع')->money('IQD'),
                TextColumn::make('status')
                ->badge()
                    ->label('الحالة')
                    ->colors([
                         'danger' => 'canceled',
                         'warning' => 'pending',
                         'primary' => 'cooking',
                         'success' => 'delivered',
                    ])
                     ->formatStateUsing(fn ($state) => match ($state) {
                        'accepted' => 'مقبول',
                        'preparing' => 'قيد التحضير',
                        'pending'  => 'معلق',
                        'cooking' => 'قيد الطهي',
                        'ready_to_receive' => 'جاهز للاستلام',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                ->label('حالة الطلب')
                ->options([
                    'on_the_way' => 'بالطريق',
                    'delivered' => 'تم التوصيل',
                    'pending' => 'معلق',
                    'accepted' => 'مقبول',
                    'cooking' => 'قيد التحضير',
                    'ready_to_receive' => 'جاهز للاستلام',
                ]),
            ])
            ->recordActions([
                   Action::make('accept')
                    ->label('قبول')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                    $oldStatus = $record->status;
                   $record->update([
                 'previous_status' => $oldStatus,
                 'status' => 'accepted',
          ]);

         event(new OrderStatusChanged(
            $record,
            $oldStatus,
            'accepted'
             ));
          }),

                // بدء الطبخ
                Action::make('cook')
                    ->label('بدء الطبخ')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'accepted')
                    ->action(function (Order $record) {
                        $oldStatus = $record->status;
                        $record->update([
                            'previous_status' => $oldStatus,
                            'status' => 'cooking'
                        ]);
                       
                        event(new OrderStatusChanged(
                            $record,
                            $oldStatus,
                            'cooking'
                        ));
                    }),

                // جاهز للاستلام
                Action::make('ready')
                    ->label('جاهز')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'cooking')
                    ->action(function (Order $record) {
               $oldStatus = $record->status;
               $record->update([
                'previous_status' => $oldStatus,
                'status' => 'ready_to_receive',
        ]);
             event(new OrderStatusChanged(
            order: $record,
            oldStatus: $oldStatus,
            newStatus: 'ready_to_receive'
        ));
    }),

                // عرض التفاصيل
               EditAction::make('edit')
                    ->label('تفاصيل الطلب'),
                ]);
        

    }
}
