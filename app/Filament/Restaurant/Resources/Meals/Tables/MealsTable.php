<?php

namespace App\Filament\Restaurant\Resources\Meals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;





class MealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
             ImageColumn::make('image'),

           TextColumn::make('name')
                ->searchable(),

            TextColumn::make('price')
                ->money('IQD'),

            TextColumn::make('discount_price')
                ->money('IQD'),

             ToggleColumn::make('is_featured')
             ->onColor('success')
                ->label('Featured'),

           ToggleColumn::make('status')
            ->label('Status')
            ->onIcon('heroicon-m-check')
            ->offIcon('heroicon-m-x-mark')
            ->onColor('success')
            ->offColor('danger')
            ->beforeStateUpdated(function ($record, bool $state) {
        // نحول true/false إلى active/inactive
        $record->status = $state ? 'active' : 'inactive';
    })
    ->getStateUsing(fn ($record) => $record->status === 'active')
    ->updateStateUsing(function ($record, bool $state) {
        $record->update([
            'status' => $state ? 'active' : 'inactive',
        ]);
    })
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
