<?php

namespace App\Filament\Resources\Restaurants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\User;



class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->required(),

            Textarea::make('description'),

            TextInput::make('address'),

            TextInput::make('lat')
                ->numeric()
                ->required(),

            TextInput::make('lng')
                ->numeric()
                ->required(),

            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                ])
                ->required(),

            Select::make('owner_id')
                ->label('Owner')
                ->options(
                    User::where('role_id', 2)->pluck('name', 'id')
                )
                ->searchable()
                ->nullable()
                ->unique(),
            ]);
    }
}
