<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('رقم الطلب'),
                TextColumn::make('user.name')->label('الزبون'),
                TextColumn::make('total')->label('المجموع')->money('IQD'),
                TextColumn::make('status')
                    ->badge()
                    ->label('الحالة')
                    ->colors([
                        'danger' => 'rejected',
                        'warning' => 'pending',
                        'success' => 'accepted',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'accepted' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'pending'  => 'معلق',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                 DeleteBulkAction::make(),
                    Action::make('قبول')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('success')
                    ->action(fn($record) => $record->update(['status' => 'accepted'])),

                  Action::make('رفض')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('danger')
                    ->action(fn($record) => $record->update(['status' => 'rejected'])),
                  
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                   
                ]),
            ]);
    }
}
