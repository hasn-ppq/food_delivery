<?php

namespace App\Filament\Restaurant\Resources\Restaurants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;

class RestaurantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

           ToggleColumn::make('status')
              ->label('Status')
              ->onIcon('heroicon-o-lock-open')
              ->offIcon('heroicon-o-lock-closed')
              ->onColor('success')
              ->offColor('danger')
              ->getStateUsing(fn ($record) => $record->status === 'open')
              ->updateStateUsing(function ($record, bool $state) {
          $record->update([
            'status' => $state ? 'open' : 'closed',
         ]);
     }),

            TextColumn::make('min_order_price')
                ->label('Min Order'),

            TextColumn::make('delivery_time_estimation')
                ->label('Delivery Time (min)')
                ->sortable(),

           TextColumn::make('delivery_price_default')
                ->label('Delivery Price'),

            TextColumn::make('created_at')
                ->dateTime()
                ->label('Created'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                
            ])->paginated(false);
    }
}
