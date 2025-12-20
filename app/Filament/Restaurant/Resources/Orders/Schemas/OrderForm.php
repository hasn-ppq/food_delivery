<?php

namespace App\Filament\Restaurant\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'preparing' => 'Preparing',
            'on_the_way' => 'On the way',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
               
            ]);
    }
}
