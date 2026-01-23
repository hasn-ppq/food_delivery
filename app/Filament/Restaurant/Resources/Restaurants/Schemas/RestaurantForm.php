<?php

namespace App\Filament\Restaurant\Resources\Restaurants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\ViewField;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;

class RestaurantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
             Hidden::make('owner_id')
             ->default(fn () => Auth::id())
             ->dehydrated(true),
               TextInput::make('name')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->rows(3),

            TextInput::make('address')
                ->label('Address'),
              

           ViewField::make('map')
              ->label('تحديد موقع المطعم')
              ->view('forms.leaflet-picker')
              ->columnSpanFull(),

            Hidden::make('lat')
              ->required(),
            Hidden ::make('lng')
              ->required(),
            Select::make('status')
                ->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                ])
                ->default('closed'),

            FileUpload::make('cover_image')
                ->directory('restaurants/covers')
                ->image(),

            TextInput::make('min_order_price')
                ->numeric()
                ->default(0),

            TextInput::make('delivery_time_estimation')
                ->numeric()
                ->label('Delivery Time (minutes)'),

            TextInput::make('delivery_price_default')
                ->numeric()
                ->default(0),
                 
            ]);
    }
}
