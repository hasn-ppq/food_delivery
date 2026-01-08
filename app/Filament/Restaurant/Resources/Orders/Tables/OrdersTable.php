<?php

namespace App\Filament\Restaurant\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
 use Filament\Tables\Actions\ViewAction;

use App\Events\OrderStatusChanged;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('id')->label('رقم الطلب'),
                TextColumn::make('customer.name')->label('الزبون'),
                TextColumn::make('total_price')->label('المجموع')->money('IQD'),
                TextColumn::make('status')
                ->badge()
                    ->label('الحالة')
                    ->colors([
                        'danger' => 'rejected',
                        'preparing' => 'Preparing',
                        'on_the_way' => 'On the way',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                     ->formatStateUsing(fn ($state) => match ($state) {
                        'accepted' => 'مقبول',
                        'preparing' => 'قيد التحضير',
                        'pending'  => 'معلق',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                   Action::make('accept')
                    ->label('قبول')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) =>
                        $record->update(['status' => 'accepted'])
                    ),

                // بدء الطبخ
                Action::make('cook')
                    ->label('بدء الطبخ')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'accepted')
                    ->action(fn ($record) =>
                        $record->update(['status' => 'cooking'])
                    ),

                // جاهز للاستلام
                Action::make('ready')
                    ->label('جاهز')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'cooking')
                    ->action(fn ($record) =>
                        $record->update(['status' => 'ready_to_receive'])
                    ),

                // عرض التفاصيل
               EditAction::make('edit')
                    ->label('تفاصيل الطلب'),
                ]);
        

    }
}
