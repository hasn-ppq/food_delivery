<?php

namespace App\Filament\Driver\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderStatusChanged;


class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('id')->label('رقم الطلب'),
                 TextColumn::make('restaurant.name')->label('المطعم'),
                 TextColumn::make('customer.name')->label('الزبون'),
                 TextColumn::make('status')->label('الحالة'),
                 TextColumn::make('total')->label('المجموع')->money('IQD'),
            ])
            ->filters([
                //
            ])
           
            ->recordActions([
               EditAction::make(),
                 Action::make('استلام')
                    ->visible(fn($record) => $record->status === 'preparing' && $record->driver_id)
                    ->color('info')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'on_the_way',
                            'driver_id' => Auth::id(),
                        ]);
                        event(new OrderStatusChanged($record));
                    }),

                Action::make('تم التوصيل')
                    ->visible(fn($record) => $record->status === 'on_the_way' && $record->driver_id === Auth::id())
                    ->color('success')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'delivered',
                        ]);
                        event(new OrderStatusChanged($record));
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
