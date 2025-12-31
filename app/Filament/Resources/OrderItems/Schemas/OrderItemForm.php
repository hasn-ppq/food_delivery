<?php

namespace App\Filament\Resources\OrderItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use App\Models\meal;



class OrderItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            
             
    ->schema([
        Select::make('mael_id')
            ->label('Meal')
            ->relationship('mael', 'name')
            ->searchable()
            ->required(),        

        TextInput::make('price')
            ->label('Price')
            ->numeric()
            ->required()
            ->reactive(),

        TextInput::make('quantity')
            ->label('Qty')
            ->numeric()
            ->default(1)
            ->reactive()
            ->afterStateUpdated(fn ($state, callable $set, callable $get) =>
                $set('total', $get('price') * $state)
            ),

        TextInput::make('total')
            ->label('Total')
            ->numeric()
            ->disabled()
            ->dehydrated(),
    ])
    ->columns(4)
    ->live();
   
    
            

    }
}
