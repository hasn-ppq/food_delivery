<?php

namespace App\Filament\Restaurant\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Events\OrderStatusChanged;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('id')->label('رقم الطلب'),
                TextColumn::make('customer.name')->label('الزبون'),
                TextColumn::make('total')->label('المجموع')->money('IQD'),
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
                 Action::make('قبول')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'preparing']);
                        event(new OrderStatusChanged($record));
                    }),
                     Action::make('تم- التحضير')
                    ->visible(fn($record) => $record->status === 'preparing')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'on_the_way']);
                        event(new OrderStatusChanged($record));
                    }),
                    EditAction::make()
                
                
           
            ]);

    }
}
