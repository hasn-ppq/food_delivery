<?php

namespace App\Filament\Driver\Resources\Orders\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumnFactory;



use function Laravel\Prompts\text;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'Items';

    


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('meal_name')
            ->columns([

                TextColumn::make('meal_name')
                    ->label('Meal'),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IQD'),

                TextColumn::make('total')
                    ->money('IQD'),
                    TextColumn::make('order.total_price')->label('السعر الكلي')->money('IQD')->getStateUsing(function ($record) {
                        $firstItem = $record->order->items->first();
                        return $record->id == $firstItem->id ? $record->order->total_price : '';
                    }),

                TextColumn::make('order.customer_address')
                    ->label('عنوان الزبون')
                    ->getStateUsing(function ($record) {
                        $firstItem = $record->order->items->first();
                        return $record->id == $firstItem->id ? $record->order->customer_address : '';

                    }),


            ]);
    }
}
