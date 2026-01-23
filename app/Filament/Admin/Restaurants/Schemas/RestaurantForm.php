<?php

namespace App\Filament\Admin\Restaurants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use App\Models\User;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Hidden;



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
            ViewField::make('map')
              ->label('تحديد موقع المطعم')
              ->view('forms.leaflet-picker')
              ->columnSpanFull(),

            Hidden::make('lat')
              ->required(),
            Hidden ::make('lng')
              ->required(),

            ]);
    }
}
